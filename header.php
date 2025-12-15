<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("location:index.php?alert=belum_login");
    exit;
}
include 'konektor.php';

$base_url = "http://localhost/peminjaman-arsip/";

$active = function ($file) {
    return strpos($_SERVER['PHP_SELF'], $file) !== false ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | E-Arsip</title>

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            background: #f8fafc;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 12px;
            font-size: 15px;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            border-radius: 6px;
            background: transparent;
            border: none;
            transition: 0.2s;
            margin-bottom: 4px;
            cursor: pointer;
        }

        .menu-item:hover {
            background: #eef2ff;
            color: #4f46e5;
        }

        .menu-item.active {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        .menu-item i.icon-start {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .submenu .menu-item {
            padding-left: 40px;
            font-size: 14px;
        }

        .rotate {
            transform: rotate(90deg);
            transition: 0.2s;
        }

        .menu-logout {
            color: #dc2626;
        }

        .menu-logout:hover {
            background: #fef2f2;
            color: #b91c1c;
        }
    </style>
</head>

<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <aside class="sidebar d-none d-md-flex flex-column">

        <div class="p-3 text-white text-center fw-bold" style="background:#4f46e5;">
            <i class="fas fa-archive me-2"></i> E-Arsip
        </div>

        <nav class="flex-grow-1 p-3 overflow-auto">

            <p class="text-uppercase small text-muted fw-semibold mb-2">Menu Utama</p>

            <a href="<?= $base_url ?>dashboard.php" class="menu-item <?= $active('dashboard.php') ?>">
                <div class="d-flex align-items-center">
                    <i class="fas fa-home icon-start"></i> Dashboard
                </div>
            </a>

            <!-- DROPDOWN -->
            <button class="menu-item" onclick="toggleDropdown('arsipMenu')">
                <div class="d-flex align-items-center">
                    <i class="fas fa-folder icon-start"></i> Penyimpanan Arsip
                </div>
                <i id="icon-arsipMenu" class="fas fa-chevron-right"></i>
            </button>

            <div id="arsipMenu" class="submenu d-none">
                <a href="arsip-vital.php" class="menu-item">Arsip Vital</a>
                <a href="arsip-permanen.php" class="menu-item">Arsip Permanen</a>
                <a href="arsip-aktif.php" class="menu-item">Arsip Aktif</a>
                <a href="arsip-inaktif.php" class="menu-item">Arsip Inaktif</a>
            </div>

            <a href="rekap.php" class="menu-item <?= $active('rekap.php') ?>">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-bar icon-start"></i> Rekap
                </div>
            </a>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <p class="text-uppercase small text-muted fw-semibold mt-4 mb-2">Administrator</p>
                <a href="user.php" class="menu-item <?= $active('user.php') ?>">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users icon-start"></i> Data User
                    </div>
                </a>
            <?php endif; ?>

            <hr>

            <a href="auth.php?logout=true" class="menu-item menu-logout">
                <div class="d-flex align-items-center">
                    <i class="fas fa-sign-out-alt icon-start"></i> Logout
                </div>
            </a>

        </nav>

        <!-- USER INFO -->
        <div class="p-3 border-top bg-light">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                     style="width:36px;height:36px;">
                    <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
                </div>
                <div class="ms-2">
                    <div class="fw-semibold"><?= $_SESSION['nama_lengkap'] ?></div>
                    <small class="text-muted text-capitalize"><?= $_SESSION['role'] ?></small>
                </div>
            </div>
        </div>

    </aside>
    <!-- CONTENT -->
    <main class="flex-grow-1 p-4">



<script>
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const icon = document.getElementById("icon-" + id);

    menu.classList.toggle("d-none");
    icon.classList.toggle("rotate");
}
</script>


