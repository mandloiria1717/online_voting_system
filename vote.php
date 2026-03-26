<?php
// ============================================================
// voter/vote.php - Cast Vote Page
// ============================================================
require_once '../includes/auth.php';
requireVoterLogin();

$voter_id  = $_SESSION['voter_id'];
$has_voted = $_SESSION['has_voted'];
$title     = getElectionTitle($conn);

// Check voting status from DB
$voting_status = getVotingStatus($conn);

// Prevent if already voted or voting closed
if ($has_voted) {
    header("Location: dashboard.php?error=You+have+already+voted");
    exit();
}
if ($voting_status !== 'on') {
    header("Location: dashboard.php?error=Voting+is+currently+closed");
    exit();
}

$error   = '';
$success = '';

// ---- Handle Vote Submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = (int)$_POST['candidate_id'];

    // Double-check user hasn't voted (DB level)
    $chk = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
    $chk->bind_param("i", $voter_id);
    $chk->execute();
    $already = $chk->get_result()->fetch_assoc();
    $chk->close();

    if ($already['has_voted']) {
        $error = "You have already voted!";
    } elseif ($candidate_id <= 0) {
        $error = "Invalid candidate selected.";
    } else {
        // Verify candidate exists
        $cv = $conn->prepare("SELECT id FROM candidates WHERE id = ?");
        $cv->bind_param("i", $candidate_id);
        $cv->execute();
        $cv->store_result();
        $exists = $cv->num_rows > 0;
        $cv->close();

        if (!$exists) {
            $error = "Candidate not found.";
        } else {
            // Begin transaction
            $conn->begin_transaction();
            try {
                // Insert vote record
                $v = $conn->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
                $v->bind_param("ii", $voter_id, $candidate_id);
                $v->execute();
                $v->close();

                // Increment candidate vote count
                $u = $conn->prepare("UPDATE candidates SET vote_count = vote_count + 1 WHERE id = ?");
                $u->bind_param("i", $candidate_id);
                $u->execute();
                $u->close();

                // Mark user as voted
                $m = $conn->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
                $m->bind_param("i", $voter_id);
                $m->execute();
                $m->close();

                $conn->commit();

                $_SESSION['has_voted'] = 1;
                header("Location: vote_success.php");
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Voting failed. Please try again.";
            }
        }
    }
}

// Fetch all candidates
$candidates = $conn->query("SELECT * FROM candidates ORDER BY position, name")->fetch_all(MYSQLI_ASSOC);

// Group by position
$grouped = [];
foreach ($candidates as $c) {
    $grouped[$c['position']][] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cast Vote – <?= $title ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-animated"></div>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand"><span class="icon">🗳️</span> <?= $title ?></div>
        <ul class="navbar-nav">
            <li><a href="dashboard.php" class="nav-link">🏠 Dashboard</a></li>
            <li><a href="vote.php" class="nav-link active">✔ Vote</a></li>
            <li><a href="results.php" class="nav-link">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div class="section-header">
        <h2 class="section-title">🗳️ Cast Your Vote</h2>
        <span class="badge badge-success">Voting Open</span>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-auto">⚠ <?= clean($error) ?></div>
    <?php endif; ?>

    <div class="alert alert-info" style="margin-bottom:24px;">
        ℹ️ Select one candidate per position. Your vote is <strong>final and cannot be changed</strong>.
    </div>

    <form method="POST" onsubmit="return validateVote()">
        <input type="hidden" name="candidate_id" id="candidate_id" value="">

        <!-- Selected info -->
        <div id="selected-info" class="hidden"></div>

        <?php foreach ($grouped as $position => $cands): ?>
        <div style="margin-bottom:40px;">
            <h3 style="margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--card-border);">
                🎯 Position: <span class="text-highlight"><?= clean($position) ?></span>
            </h3>
            <div class="grid grid-3">
                <?php foreach ($cands as $c): ?>
                <div class="candidate-card" data-id="<?= $c['id'] ?>"
                     onclick="selectCandidate(<?= $c['id'] ?>, '<?= clean($c['name']) ?>')">
                    <div class="candidate-avatar">
                        <?= strtoupper(substr($c['name'], 0, 1)) ?>
                    </div>
                    <div class="candidate-name"><?= clean($c['name']) ?></div>
                    <div class="candidate-position"><?= clean($c['position']) ?></div>
                    <div class="candidate-party">🏛️ <?= clean($c['party']) ?></div>
                    <?php if ($c['bio']): ?>
                    <div class="candidate-bio"><?= clean($c['bio']) ?></div>
                    <?php endif; ?>
                    <div style="margin-top:14px;">
                        <span style="font-size:0.8rem;color:var(--text-muted);">Click to select</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($candidates)): ?>
            <div class="card text-center" style="padding:60px;">
                <p style="font-size:3rem;">🏁</p>
                <h3>No candidates available yet.</h3>
            </div>
        <?php else: ?>
        <div class="text-center" style="margin-top:32px;">
            <button type="submit" id="btn-vote" class="btn btn-primary btn-lg" disabled>
                Select a candidate above
            </button>
        </div>
        <?php endif; ?>
    </form>

</div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
