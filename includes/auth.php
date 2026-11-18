<?php

// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// AUTENTIKASI ADMIN
// ==========================================

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header("Location: /PWL/login.php");
        exit;
    }
}

function redirectIfLoggedIn()
{
    if (isAdminLoggedIn()) {
        header("Location: /PWL/admin/dashboard.php");
        exit;
    }
}

// ==========================================
// AUTENTIKASI MAHASISWA
// ==========================================

function isStudentLoggedIn()
{
    return isset($_SESSION['mahasiswa_id']);
}

function requireStudent()
{
    if (!isStudentLoggedIn()) {
        header("Location: /PWL/mahasiswa/login.php");
        exit;
    }
}

function redirectIfStudentLoggedIn()
{
    if (isStudentLoggedIn()) {
        header("Location: /PWL/mahasiswa/dashboard.php");
        exit;
    }
}