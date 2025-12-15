<?php
session_start();
include "konektor.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$captcha_input = $_POST['captcha_input'] ?? '';

// VALIDASI CAPTCHA
if ($captcha_input != $_SESSION['captcha']) {
    unset($_SESSION['captcha']); // reset captcha
    header("Location: index.php?alert=captcha");
    exit;
}

// CEK USER
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

// Jika user tidak ditemukan atau password salah
if (!$user || $password !== $user['password']) {
    unset($_SESSION['captcha']); // reset captcha
    header("Location: index.php?alert=gagal");
    exit;
}

// Jika login berhasil
$_SESSION['login'] = true;
$_SESSION['username'] = $user['username'];
$_SESSION['nama_lengkap'] = $user['nama_lengkap'];
$_SESSION['role'] = $user['role'];


unset($_SESSION['captcha']); // reset setelah login sukses

header("Location: dashboard.php");
exit;
