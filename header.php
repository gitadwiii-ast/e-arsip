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

$current_file = basename($_SERVER['PHP_SELF']);

$arsip_active = (
    strpos($current_file, 'arsip-vital') !== false || 
    strpos($current_file, 'arsip-permanen') !== false || 
    strpos($current_file, 'arsip-aktif') !== false || 
    strpos($current_file, 'arsip-inaktif') !== false
);
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
    /* --- SIDEBAR STYLE UTAMA --- */
    .sidebar {
        width: 260px;          /* Lebar dasar */
        min-width: 260px;      /* PENTING: Mencegah sidebar mengecil saat konten kanan lebar */
        height: 100vh;         /* Tinggi sepenuh layar */
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        
        /* Agar sidebar tetap diam saat konten di scroll (opsional, tapi disarankan) */
        position: sticky; 
        top: 0;
        
        /* Flex setup */
        display: flex;
        flex-direction: column;
        flex-shrink: 0;        /* PENTING: Mencegah sidebar 'gepeng' */
        z-index: 100;          /* Agar selalu di atas jika ada elemen numpuk */
    }

    /* --- MENU ITEM STYLE --- */
    .menu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 12px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        border-radius: 0.375rem;
        background: transparent;
        border: none;
        transition: all 0.2s;
        margin-bottom: 4px;
        cursor: pointer;
        text-align: left;
    }

    .menu-item:hover {
        background: #EEF2FF;
        color: #4F46E5;
    }

    .menu-item.active {
        background: #EEF2FF;
        color: #4F46E5;
        font-weight: 600;
    }

    .menu-item.active i {
        color: #4F46E5;
    }

    .menu-item .icon-start {
        width: 20px;
        text-align: center;
        margin-right: 0.75rem;
    }

    .rotate {
        transform: rotate(90deg);
        transition: transform 0.2s;
    }

    /* Indentasi khusus Submenu */
    .submenu .menu-item {
        padding-left: 2.75rem !important; /* Memberi jarak menjorok ke dalam */
        font-size: 0.9rem;                /* Ukuran font sedikit lebih kecil */
        
        display: flex;                    /* Menggunakan flexbox */
        align-items: center;              /* Vertikal: Rata tengah */
        justify-content: flex-start;      /* Horizontal: Rata KIRI (PENTING) */
        text-align: left;                 /* Memastikan teks rata kiri */
    }

    /* Styling khusus Icon di dalam Submenu */
    .submenu .menu-item i {
        font-size: 0.85rem; 
        width: 20px;        /* Lebar tetap agar teks di sebelahnya lurus rapi */
        text-align: center; /* Icon di tengah kotaknya sendiri */
        margin-right: 12px; /* Jarak antara icon dan teks */
        flex-shrink: 0;     /* Mencegah icon gepeng */
        opacity: 0.75;
    }
    
    .submenu .menu-item:hover i {
        opacity: 1;
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
            <button class="menu-item <?= $arsip_active ? 'active' : '' ?>" onclick="toggleDropdown('arsipDinamis')">
            <div class="d-flex align-items-center">
                <i class="fas fa-folder icon-start"></i>
                <span>Penyimpanan Arsip</span>
            </div>
            <i id="icon-arsipDinamis" class="fas fa-chevron-right small <?= $arsip_active ? 'rotate' : '' ?>"></i>
        </button>

        <div id="arsipDinamis" class="submenu <?= $arsip_active ? '' : 'd-none' ?>">
            
            <a href="<?= $base_url ?>arsip-vital.php" class="menu-item <?= strpos($current_file, 'arsip-vital') !== false ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i>
                <span>Arsip Vital</span>
            </a>

            <a href="<?= $base_url ?>arsip-permanen.php" class="menu-item <?= strpos($current_file, 'arsip-permanen') !== false ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i>
                <span>Arsip Permanen</span>
            </a>

            <a href="<?= $base_url ?>arsip-aktif.php" class="menu-item <?= $current_file == 'arsip-aktif.php' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i>
                <span>Arsip Aktif</span>
            </a>

            <a href="<?= $base_url ?>arsip-inaktif.php" class="menu-item <?= $current_file == 'arsip-inaktif.php' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i>
                <span>Arsip Inaktif</span>
            </a>

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


