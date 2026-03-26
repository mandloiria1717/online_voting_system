<?php
// ============================================================
// admin/login.php - Admin Login
// ============================================================
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Redirect if already logged in as admin
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Admin not found.";
        }
        $stmt->close();
    }
}

if (!$error && isset($_GET['error'])) $error = htmlspecialchars($_GET['error']);

$title = getElectionTitle($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login – <?= $title ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-animated"></div>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-icon" style="background:linear-gradient(135deg,#f5a623,#e94560);">🔐</div>
            <h2>Admin Panel</h2>
            <p><?= $title ?></p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto">⚠ <?= clean($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="admin-pass" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-gold btn-block btn-lg mt-8">
                🔐 Login as Admin
            </button>
        </form>

        <p class="text-center mt-16 text-muted" style="font-size:0.85rem;">
            <a href="../index.php">← Back to Voter Login</a>
        </p>

        <div class="alert alert-info mt-16" style="font-size:0.82rem;">
            Default: <span class="font-mono">admin</span> / <span class="font-mono">admin123</span><br>
            <small style="color:var(--text-muted);">(Change after setup!)</small>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
