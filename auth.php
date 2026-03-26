<?php
// ============================================================
// includes/auth.php - Authentication Helper Functions
// ============================================================

require_once 'config.php';

// Redirect if NOT logged in as voter
function requireVoterLogin() {
    if (!isset($_SESSION['voter_id'])) {
        header("Location: ../index.php?error=Please+login+first");
        exit();
    }
}

// Redirect if NOT logged in as admin
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php?error=Admin+access+required");
        exit();
    }
}

// Get voting status from DB
function getVotingStatus($conn) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'voting_status'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['setting_value'] ?? 'off';
}

// Get election title
function getElectionTitle($conn) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'election_title'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result['setting_value'] ?? 'Online Election';
}

// Sanitize output
function clean($str) {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}
?>
