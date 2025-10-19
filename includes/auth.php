<?php
// Basic authentication functions
// This is a placeholder - implement actual authentication logic as needed

function isLoggedIn() {
    return isset($_SESSION['user']) || isset($_SESSION['admin_logged_in']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php?page=login");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php?page=login");
        exit;
    }
}
?>