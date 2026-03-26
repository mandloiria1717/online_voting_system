<?php
// ============================================================
// admin/results.php - Admin Results View
// ============================================================
require_once '../includes/auth.php';
requireAdminLogin();

$title = getElectionTitle($conn);
$voting_status = getVotingStatus($conn);

// Fetch candidates ordered by position and vote count
$candidates = $conn->query("SELECT * FROM candidates ORDER BY position, vote_count DESC")->fetch_all(MYSQLI_ASSOC);

$total_votes   = $conn->query("SELECT COUNT(*) as c FROM votes")->fetch_assoc()['c'];
$total_voters  = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$voted_count   = $conn->query("SELECT COUNT(*) as c FROM users WHERE has_voted=1")->fetch_assoc()['c'];

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
<title>Results – <?= $title ?></title>
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
            <li><a href="candidates.php" class="nav-link">🏅 Candidates</a></li>
            <li><a href="voters.php" class="nav-link">👥 Voters</a></li>
            <li><a href="results.php" class="nav-link active">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div class="section-header">
        <h2 class="section-title">📊 Election Results</h2>
        <?php if ($voting_status === 'on'): ?>
            <span class="badge badge-success">🟢 Live</span>
        <?php else: ?>
            <span class="badge badge-warning">Final</span>
        <?php endif; ?>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-4" style="margin-bottom:32px;">
        <div class="stat-card">
            <div class="stat-icon blue">🗳️</div>
            <div class="stat-info"><div class="value"><?= $total_votes ?></div><div class="label">Total Votes</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">👥</div>
            <div class="stat-info"><div class="value"><?= $total_voters ?></div><div class="label">Total Voters</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold">📈</div>
            <div class="stat-info">
                <div class="value"><?= $total_voters > 0 ? round($voted_count/$total_voters*100) : 0 ?>%</div>
                <div class="label">Turnout</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">🏅</div>
            <div class="stat-info"><div class="value"><?= count($candidates) ?></div><div class="label">Candidates</div></div>
        </div>
    </div>

    <!-- Results by Position -->
    <?php if (empty($candidates)): ?>
        <div class="card text-center" style="padding:60px;">
            <p style="font-size:3rem;">📋</p>
            <h3>No candidates or votes yet.</h3>
        </div>
    <?php endif; ?>

    <?php foreach ($grouped as $position => $cands): ?>
    <?php
        $pos_total = array_sum(array_column($cands, 'vote_count'));
        $winner = $cands[0]; // First = highest votes
    ?>
    <div class="card" style="margin-bottom:28px;">
        <div class="flex-between" style="margin-bottom:24px;">
            <h3 style="color:var(--text);">🎯 <?= clean($position) ?></h3>
            <span style="font-size:0.85rem; color:var(--text-muted);"><?= $pos_total ?> total votes</span>
        </div>

        <?php if ($voting_status === 'off' && $winner['vote_count'] > 0): ?>
        <div style="background:rgba(245,166,35,0.1);border:1px solid rgba(245,166,35,0.3);border-radius:12px;padding:16px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
            <span style="font-size:2rem;">👑</span>
            <div>
                <div style="font-weight:700;color:var(--gold);">WINNER: <?= clean($winner['name']) ?></div>
                <div style="font-size:0.85rem;color:var(--text-muted);"><?= $winner['vote_count'] ?> votes (<?= $pos_total > 0 ? round($winner['vote_count']/$pos_total*100,1) : 0 ?>%)</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Candidate</th>
                        <th>Party</th>
                        <th>Votes</th>
                        <th>Percentage</th>
                        <th>Vote Share</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cands as $rank => $c): ?>
                    <?php $pct = $pos_total > 0 ? round($c['vote_count']/$pos_total*100,1) : 0; ?>
                    <tr style="<?= $rank===0 && $c['vote_count']>0 ? 'background:rgba(245,166,35,0.05);' : '' ?>">
                        <td>
                            <?php if ($rank===0 && $c['vote_count']>0): ?>
                                <span style="font-size:1.2rem;">👑</span>
                            <?php else: ?>
                                <span style="color:var(--text-muted);">#<?= $rank+1 ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--text);"><?= clean($c['name']) ?></div>
                        </td>
                        <td style="font-size:0.85rem;color:var(--text-muted);"><?= clean($c['party']) ?></td>
                        <td class="font-mono" style="font-weight:700;color:var(--text);"><?= $c['vote_count'] ?></td>
                        <td class="font-mono"><?= $pct ?>%</td>
                        <td style="width:180px;">
                            <div class="progress-bar" style="height:8px;">
                                <div class="progress-fill" data-width="<?= $pct ?>"
                                    style="background:<?= $rank===0 ? 'linear-gradient(90deg,var(--gold),var(--highlight))' : 'linear-gradient(90deg,var(--accent),var(--highlight))' ?>;">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>

</div>
</div>

<script src="../assets/js/main.js"></script>
<?php if ($voting_status === 'on'): ?>
<script>setTimeout(() => location.reload(), 30000);</script>
<?php endif; ?>
</body>
</html>
