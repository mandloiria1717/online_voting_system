<?php
// ============================================================
// admin/candidates.php - Manage Candidates
// ============================================================
require_once '../includes/auth.php';
requireAdminLogin();

$title = getElectionTitle($conn);
$error = '';
$success = '';

// ---- Add Candidate ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name     = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $party    = trim($_POST['party'] ?? '');
        $bio      = trim($_POST['bio'] ?? '');

        if (strlen($name) < 2)     $error = "Candidate name is required.";
        elseif (empty($position))  $error = "Position is required.";
        else {
            $stmt = $conn->prepare("INSERT INTO candidates (name, position, party, bio) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $position, $party, $bio);
            if ($stmt->execute()) {
                $success = "Candidate '$name' added successfully!";
            } else {
                $error = "Failed to add candidate.";
            }
            $stmt->close();
        }
    }

    // ---- Delete Candidate ----
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['candidate_id'];
        $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success = "Candidate removed.";
        } else {
            $error = "Could not delete candidate.";
        }
        $stmt->close();
    }
}

// Fetch all candidates
$candidates = $conn->query("SELECT * FROM candidates ORDER BY position, name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Candidates – <?= $title ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-animated"></div>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand"><span class="icon">⚙️</span> Admin Panel</div>
        <ul class="navbar-nav">
            <li><a href="dashboard.php" class="nav-link">🏠 Dashboard</a></li>
            <li><a href="candidates.php" class="nav-link active">🏅 Candidates</a></li>
            <li><a href="voters.php" class="nav-link">👥 Voters</a></li>
            <li><a href="results.php" class="nav-link">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div class="section-header">
        <h2 class="section-title">🏅 Manage Candidates</h2>
        <span class="badge badge-gold"><?= count($candidates) ?> total</span>
    </div>

    <?php if ($error): ?><div class="alert alert-danger alert-auto">⚠ <?= clean($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success alert-auto">✔ <?= clean($success) ?></div><?php endif; ?>

    <div class="grid grid-2" style="gap:32px;">

        <!-- Add Candidate Form -->
        <div class="card">
            <h3 style="margin-bottom:24px;">➕ Add New Candidate</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Candidate full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Position *</label>
                    <input type="text" name="position" class="form-control" placeholder="e.g. President, Secretary">
                    <small style="color:var(--text-muted);font-size:0.8rem;">Must match exactly for same position grouping</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Party / Group</label>
                    <input type="text" name="party" class="form-control" placeholder="e.g. Progress Party">
                </div>

                <div class="form-group">
                    <label class="form-label">Short Bio</label>
                    <textarea name="bio" class="form-control" placeholder="Brief description of the candidate..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">➕ Add Candidate</button>
            </form>
        </div>

        <!-- Candidates List -->
        <div>
            <h3 style="margin-bottom:16px; color:var(--text);">Current Candidates</h3>

            <?php if (empty($candidates)): ?>
                <div class="card text-center" style="padding:40px;">
                    <p style="font-size:2rem;">📋</p>
                    <p>No candidates added yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($candidates as $c): ?>
                <div class="card" style="margin-bottom:14px; padding:18px; animation-delay:0.1s;">
                    <div class="flex-between">
                        <div class="flex gap-12" style="align-items:center;">
                            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--highlight));display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0;">
                                <?= strtoupper(substr($c['name'],0,1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600; color:var(--text);"><?= clean($c['name']) ?></div>
                                <div style="font-size:0.82rem; color:var(--highlight);"><?= clean($c['position']) ?></div>
                                <?php if ($c['party']): ?>
                                <div style="font-size:0.78rem; color:var(--text-muted);">🏛️ <?= clean($c['party']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex gap-8" style="align-items:center;">
                            <span class="badge badge-gold font-mono"><?= $c['vote_count'] ?> votes</span>
                            <form method="POST" style="display:inline;" onsubmit="return confirmAction('Delete this candidate? Their votes will also be removed.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="candidate_id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php if ($c['bio']): ?>
                    <p style="font-size:0.82rem;color:var(--text-muted);margin-top:10px;padding-top:10px;border-top:1px solid var(--card-border);">
                        <?= clean($c['bio']) ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

</div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
