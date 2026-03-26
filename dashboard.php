<?php
// ============================================================
// admin/dashboard.php - Admin Dashboard
// ============================================================
require_once '../includes/auth.php';
requireAdminLogin();

$title = getElectionTitle($conn);
$voting_status = getVotingStatus($conn);

// ---- Handle Start/Stop Voting ----
if (isset($_POST['toggle_voting'])) {
    $new_status = ($voting_status === 'on') ? 'off' : 'on';
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'voting_status'");
    $stmt->bind_param("s", $new_status);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit();
}

// Stats
$total_voters    = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$voted_count     = $conn->query("SELECT COUNT(*) as c FROM users WHERE has_voted=1")->fetch_assoc()['c'];
$not_voted       = $total_voters - $voted_count;
$candidate_count = $conn->query("SELECT COUNT(*) as c FROM candidates")->fetch_assoc()['c'];
$total_votes     = $conn->query("SELECT COUNT(*) as c FROM votes")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard – <?= $title ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-animated"></div>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand"><span class="icon">⚙️</span> Admin Panel</div>
        <ul class="navbar-nav">
            <li><a href="dashboard.php" class="nav-link active">🏠 Dashboard</a></li>
            <li><a href="candidates.php" class="nav-link">🏅 Candidates</a></li>
            <li><a href="voters.php" class="nav-link">👥 Voters</a></li>
            <li><a href="results.php" class="nav-link">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div style="margin-bottom:32px; animation:fadeInUp 0.5s ease;">
        <h1>Dashboard <span class="text-muted" style="font-size:1.2rem;font-weight:400;">– <?= clean($_SESSION['admin_name']) ?></span></h1>
        <p>Manage the election from this panel.</p>
    </div>

    <!-- Voting Control -->
    <div class="card" style="margin-bottom:32px;animation-delay:0.1s;">
        <div class="flex-between">
            <div>
                <h3>⚡ Voting Control</h3>
                <p style="margin-top:6px;">
                    Current status:
                    <?php if ($voting_status === 'on'): ?>
                        <span class="badge badge-success" style="font-size:0.9rem;">🟢 VOTING OPEN</span>
                    <?php else: ?>
                        <span class="badge badge-danger" style="font-size:0.9rem;">🔴 VOTING CLOSED</span>
                    <?php endif; ?>
                </p>
            </div>
            <form method="POST" onsubmit="return confirmAction('<?= $voting_status === 'on' ? 'Stop voting? Voters cannot vote after this.' : 'Open voting? Voters can now cast their votes.' ?>')">
                <button type="submit" name="toggle_voting"
                    class="btn btn-lg <?= $voting_status === 'on' ? 'btn-danger' : 'btn-success' ?>">
                    <?= $voting_status === 'on' ? '⏹ Stop Voting' : '▶ Start Voting' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-4" style="margin-bottom:32px;">
        <div class="stat-card" style="animation-delay:0.2s">
            <div class="stat-icon blue">👥</div>
            <div class="stat-info">
                <div class="value"><?= $total_voters ?></div>
                <div class="label">Registered Voters</div>
            </div>
        </div>
        <div class="stat-card" style="animation-delay:0.3s">
            <div class="stat-icon green">✔</div>
            <div class="stat-info">
                <div class="value"><?= $voted_count ?></div>
                <div class="label">Votes Cast</div>
            </div>
        </div>
        <div class="stat-card" style="animation-delay:0.4s">
            <div class="stat-icon red">⏳</div>
            <div class="stat-info">
                <div class="value"><?= $not_voted ?></div>
                <div class="label">Yet to Vote</div>
            </div>
        </div>
        <div class="stat-card" style="animation-delay:0.5s">
            <div class="stat-icon gold">🏅</div>
            <div class="stat-info">
                <div class="value"><?= $candidate_count ?></div>
                <div class="label">Candidates</div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-3" style="animation-delay:0.6s">
        <a href="candidates.php" class="card" style="text-decoration:none;text-align:center;">
            <div style="font-size:2.5rem;">🏅</div>
            <h3 style="margin-top:12px;color:var(--text);">Manage Candidates</h3>
            <p>Add, edit, or remove candidates</p>
        </a>
        <a href="voters.php" class="card" style="text-decoration:none;text-align:center;">
            <div style="font-size:2.5rem;">👥</div>
            <h3 style="margin-top:12px;color:var(--text);">View All Voters</h3>
            <p>See registered voters & voting status</p>
        </a>
        <a href="results.php" class="card" style="text-decoration:none;text-align:center;">
            <div style="font-size:2.5rem;">📊</div>
            <h3 style="margin-top:12px;color:var(--text);">View Results</h3>
            <p>Live vote counts & winner analysis</p>
        </a>
    </div>

    <!-- Turnout Bar -->
    <div class="card" style="margin-top:32px; animation-delay:0.7s;">
        <h3 style="margin-bottom:16px;">📈 Voter Turnout</h3>
        <?php $pct = $total_voters > 0 ? round($voted_count / $total_voters * 100) : 0; ?>
        <div class="flex-between" style="margin-bottom:8px;">
            <span style="color:var(--text-muted);font-size:0.9rem;"><?= $voted_count ?> of <?= $total_voters ?> voters</span>
            <span style="font-weight:700;font-size:1.2rem;font-family:'JetBrains Mono',monospace;"><?= $pct ?>%</span>
        </div>
        <div class="progress-bar" style="height:14px;">
            <div class="progress-fill" data-width="<?= $pct ?>"></div>
        </div>
    </div>

</div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
