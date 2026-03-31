<?php
require_once __DIR__ . '/../config.php';

function startSessionIfNeeded()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool
{
    startSessionIfNeeded();
    return isset($_SESSION[USER_SESSION_KEY]);
}

function isAdmin(): bool
{
    startSessionIfNeeded();
    return isset($_SESSION[ADMIN_SESSION_KEY]);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

function requireAdmin()
{
    if (!isAdmin()) {
        header('Location: /admin/index.php');
        exit;
    }
}

function getCurrentUser(): ?array
{
    startSessionIfNeeded();
    return $_SESSION[USER_SESSION_KEY] ?? null;
}

function getCurrentAdmin(): ?array
{
    startSessionIfNeeded();
    return $_SESSION[ADMIN_SESSION_KEY] ?? null;
}

function logout()
{
    startSessionIfNeeded();
    unset($_SESSION[USER_SESSION_KEY]);
    session_destroy();
    header('Location: /index.php');
    exit;
}

function adminLogout()
{
    startSessionIfNeeded();
    unset($_SESSION[ADMIN_SESSION_KEY]);
    session_destroy();
    header('Location: /admin/index.php');
    exit;
}
?>