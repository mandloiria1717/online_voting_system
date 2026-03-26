<?php
// ============================================================
// admin/voters.php - View All Voters
// ============================================================
require_once '../includes/auth.php';
requireAdminLogin();

$title = getElectionTitle($conn);

// Search filter
$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all'; // all | voted | not_voted

$query = "SELECT id, full_name, email, student_id, has_voted, created_at FROM users WHERE 1=1";
$params = [];
$types  = "";

if ($search !== '') {
    $query .= " AND (full_name LIKE ? OR email LIKE ? OR student_id LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like, $like];
    $types = "sss";
}

if ($filter === 'voted') {
    $query .= " AND has_voted = 1";
} elseif ($filter === 'not_voted') {
    $query .= " AND has_voted = 0";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$voters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_voters = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$voted_count  = $conn->query("SELECT COUNT(*) as c FROM users WHERE has_voted=1")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Voters – <?= $title ?></title>
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
            <li><a href="voters.php" class="nav-link active">👥 Voters</a></li>
            <li><a href="results.php" class="nav-link">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
<div class="container">

    <div class="section-header">
        <h2 class="section-title">👥 All Voters</h2>
        <span class="badge badge-gold"><?= $total_voters ?> registered</span>
    </div>

    <!-- Summary -->
    <div class="grid grid-3" style="margin-bottom:28px;">
        <div class="stat-card">
            <div class="stat-icon blue">👥</div>
            <div class="stat-info"><div class="value"><?= $total_voters ?></div><div class="label">Total Registered</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✔</div>
            <div class="stat-info"><div class="value"><?= $voted_count ?></div><div class="label">Voted</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">⏳</div>
            <div class="stat-info"><div class="value"><?= $total_voters - $voted_count ?></div><div class="label">Not Yet Voted</div></div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card" style="margin-bottom:24px; padding:20px;">
        <form method="GET" class="flex gap-12" style="flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, or student ID..." value="<?= clean($search) ?>">
            </div>
            <div>
                <label class="form-label">Filter</label>
                <select name="filter" class="form-control">
                    <option value="all"       <?= $filter==='all'       ?'selected':'' ?>>All Voters</option>
                    <option value="voted"     <?= $filter==='voted'     ?'selected':'' ?>>Voted</option>
                    <option value="not_voted" <?= $filter==='not_voted' ?'selected':'' ?>>Not Voted</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">🔍 Filter</button>
                <a href="voters.php" class="btn btn-secondary" style="margin-left:8px;">✖ Clear</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Student ID</th>
                    <th>Vote Status</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($voters)): ?>
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">No voters found.</td></tr>
                <?php else: ?>
                <?php foreach ($voters as $i => $v): ?>
                <tr>
                    <td style="color:var(--text-muted); font-size:0.85rem;"><?= $i+1 ?></td>
                    <td>
                        <div style="font-weight:600;"><?= clean($v['full_name']) ?></div>
                    </td>
                    <td style="font-size:0.88rem; color:var(--text-muted);"><?= clean($v['email']) ?></td>
                    <td class="font-mono" style="font-size:0.85rem;"><?= clean($v['student_id']) ?></td>
                    <td>
                        <?php if ($v['has_voted']): ?>
                            <span class="badge badge-success">✔ Voted</span>
                        <?php else: ?>
                            <span class="badge badge-warning">⏳ Pending</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.82rem; color:var(--text-muted);">
                        <?= date('d M Y', strtotime($v['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top:12px; font-size:0.85rem; color:var(--text-muted);">
        Showing <?= count($voters) ?> result(s).
    </p>

</div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
