<?php
// ============================================================
// voter/vote_success.php - Vote Confirmation
// ============================================================
require_once '../includes/auth.php';
requireVoterLogin();

// Only show if has_voted is true
if (!$_SESSION['has_voted']) {
    header("Location: dashboard.php");
    exit();
}

$voter_name = clean($_SESSION['voter_name']);
$title = getElectionTitle($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote Confirmed – <?= $title ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.confetti { position:fixed; inset:0; pointer-events:none; z-index:99; overflow:hidden; }
.confetti-piece {
    position:absolute;
    top:-10px;
    width:10px; height:10px;
    border-radius:2px;
    animation: fall linear infinite;
}
@keyframes fall {
    to { transform: translateY(110vh) rotate(720deg); opacity:0; }
}
</style>
</head>
<body>
<div class="bg-animated"></div>
<div class="confetti" id="confetti"></div>

<!-- Navbar -->
<nav class="navbar">
    <div class="container">
        <div class="navbar-brand"><span class="icon">🗳️</span> <?= $title ?></div>
        <ul class="navbar-nav">
            <li><a href="dashboard.php" class="nav-link">🏠 Dashboard</a></li>
            <li><a href="results.php" class="nav-link">📊 Results</a></li>
            <li><a href="logout.php" class="nav-link btn-logout">↩ Logout</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper flex-center" style="min-height:80vh;">
<div class="container" style="max-width:600px;">
    <div class="card">
        <div class="confirmation-wrap">
            <span class="confirmation-icon">🎉</span>
            <h1 style="color:var(--text);margin-bottom:12px;">Vote Cast Successfully!</h1>
            <p style="font-size:1.1rem;margin-bottom:8px;">
                Thank you, <strong style="color:var(--highlight);"><?= $voter_name ?></strong>!
            </p>
            <p>Your vote has been securely recorded. Results will be available after voting closes.</p>

            <div style="background:rgba(39,174,96,0.1);border:1px solid rgba(39,174,96,0.3);border-radius:12px;padding:20px;margin:28px 0;">
                <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:6px;">CONFIRMATION</div>
                <div style="font-size:0.9rem;color:var(--success);font-family:'JetBrains Mono',monospace;">
                    ✔ Vote recorded at <?= date('d M Y, h:i A') ?>
                </div>
            </div>

            <div class="flex-center gap-12">
                <a href="results.php" class="btn btn-gold btn-lg">📊 View Results</a>
                <a href="dashboard.php" class="btn btn-secondary btn-lg">🏠 Dashboard</a>
            </div>
        </div>
    </div>
</div>
</div>

<script>
// Create confetti
const container = document.getElementById('confetti');
const colors = ['#e94560','#f5a623','#27ae60','#3498db','#9b59b6','#1abc9c'];
for (let i = 0; i < 80; i++) {
    const el = document.createElement('div');
    el.className = 'confetti-piece';
    el.style.cssText = `
        left: ${Math.random()*100}%;
        background: ${colors[Math.floor(Math.random()*colors.length)]};
        width: ${6+Math.random()*10}px;
        height: ${6+Math.random()*10}px;
        animation-duration: ${2+Math.random()*4}s;
        animation-delay: ${Math.random()*3}s;
        border-radius: ${Math.random()>0.5?'50%':'2px'};
        opacity: ${0.6+Math.random()*0.4};
    `;
    container.appendChild(el);
}
// Remove confetti after 6s
setTimeout(() => container.remove(), 7000);
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>
