<?php
// ============================================================
// index.php - Voter Login & Registration Page
// ============================================================
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isset($_SESSION['voter_id'])) {
    header("Location: voter/dashboard.php");
    exit();
}

$error   = '';
$success = '';

// ---- Handle Registration ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $full_name  = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (strlen($full_name) < 2)    $error = "Full name must be at least 2 characters.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Invalid email address.";
    elseif (strlen($student_id) < 3) $error = "Invalid student ID.";
    elseif (strlen($password) < 6) $error = "Password must be at least 6 characters.";
    elseif ($password !== $confirm) $error = "Passwords do not match.";
    else {
        // Check if email or student_id already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR student_id = ?");
        $stmt->bind_param("ss", $email, $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email or Student ID already registered.";
        } else {
            // Hash password and insert user
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt2 = $conn->prepare("INSERT INTO users (full_name, email, student_id, password) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssss", $full_name, $email, $student_id, $hashed);
            if ($stmt2->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt2->close();
        }
        $stmt->close();
    }
}

// ---- Handle Login ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password, has_voted FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Create session
                $_SESSION['voter_id']   = $user['id'];
                $_SESSION['voter_name'] = $user['full_name'];
                $_SESSION['has_voted']  = $user['has_voted'];
                header("Location: voter/dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    }
}

// URL params (from redirects)
if (!$error && isset($_GET['error'])) $error = htmlspecialchars($_GET['error']);

$title = getElectionTitle($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – <?= clean($title) ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="bg-animated"></div>

<div class="auth-wrapper">
    <div class="auth-card">
        <!-- Logo -->
        <div class="auth-logo">
            <div class="logo-icon">🗳️</div>
            <h2><?= clean($title) ?></h2>
            <p>Secure Online Voting Platform</p>
        </div>

        <!-- Tabs -->
        <div class="auth-tabs">
            <div class="auth-tab <?= (!$success) ? 'active' : '' ?>" data-tab="login" onclick="switchTab('login')">Login</div>
            <div class="auth-tab <?= ($success) ? 'active' : '' ?>" data-tab="register" onclick="switchTab('register')">Register</div>
        </div>

        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto">⚠ <?= clean($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-auto">✔ <?= clean($success) ?></div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <div id="form-login" class="auth-form <?= $success ? 'hidden' : '' ?>">
            <form method="POST" onsubmit="return validateLogin()">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="you@college.edu" autocomplete="email">
                    <small class="field-error text-highlight" id="err-login-email"></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="login-pass" name="password" class="form-control" placeholder="••••••••">
                    <small class="field-error text-highlight" id="err-login-pass"></small>
                </div>

                <button type="submit" id="btn-login" class="btn btn-primary btn-block btn-lg mt-8">
                    🔐 Login to Vote
                </button>
            </form>

            <p class="text-center mt-16 text-muted" style="font-size:0.85rem;">
                Admin? <a href="admin/login.php">Admin Panel →</a>
            </p>
        </div>

        <!-- REGISTER FORM -->
        <div id="form-register" class="auth-form <?= !$success ? 'hidden' : '' ?>">
            <form method="POST" onsubmit="return validateRegister()">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="reg-name" name="full_name" class="form-control" placeholder="Jane Doe">
                    <small class="field-error text-highlight" id="err-name"></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="reg-email" name="email" class="form-control" placeholder="you@college.edu">
                    <small class="field-error text-highlight" id="err-email"></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" id="reg-sid" name="student_id" class="form-control" placeholder="e.g. STU2024001">
                    <small class="field-error text-highlight" id="err-sid"></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="reg-pass" name="password" class="form-control" placeholder="Min. 6 characters">
                    <small class="field-error text-highlight" id="err-pass"></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" id="reg-confirm" name="confirm_password" class="form-control" placeholder="Re-enter password">
                    <small class="field-error text-highlight" id="err-confirm"></small>
                </div>

                <button type="submit" id="btn-register" class="btn btn-primary btn-block btn-lg mt-8">
                    📝 Create Account
                </button>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
