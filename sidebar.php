<?php
// session_start();
include 'konektor.php';

$base_url = "http://localhost/peminjaman-arsip";
$current_file = basename($_SERVER['PHP_SELF']);

// Helper function untuk active state
$active = function ($keyword) use ($current_file) {
    return strpos($current_file, $keyword) !== false ? 'active' : '';
};

// Logic Active Menu
$arsip_active = (
    strpos($current_file, 'arsip-vital') !== false ||
    strpos($current_file, 'arsip-permanen') !== false ||
    strpos($current_file, 'arsip-aktif') !== false ||
    strpos($current_file, 'arsip-inaktif') !== false
);
// Pengawasan removed as it's not in function
$rekap_active = (strpos($current_file, 'rekap') !== false);
?>

<style>
    /* =========================================
       1. CSS DASAR SIDEBAR
    ========================================= */
    .sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease;
        z-index: 1040;
        overflow-x: hidden;
        flex-shrink: 0;
        white-space: nowrap;
        height: 100vh;
        /* Ensure full height */
        position: sticky;
        /* Keep it sticky */
        top: 0;
    }

    .custom-scrollbar {
        overflow-y: auto;
        overflow-x: hidden;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #e5e7eb;
        border-radius: 4px;
    }

    /* =========================================
       2. STYLE MENU ITEM
    ========================================= */
    .menu-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 12px 18px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
        text-decoration: none;
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0;
    }

    .menu-item:hover {
        background: #f3f4f6;
        color: #4F46E5;
    }

    /* Active State */
    .menu-item.active {
        background: #EEF2FF;
        color: #4F46E5;
        font-weight: 600;
        border-right: 3px solid #4F46E5;
    }

    .menu-item.active i {
        color: #4F46E5;
    }

    .menu-item .icon-start {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
        margin-right: 12px;
        flex-shrink: 0;
        transition: margin 0.3s;
    }

    .menu-text,
    .menu-arrow,
    .menu-header {
        opacity: 1;
        transition: opacity 0.2s ease;
    }

    .menu-arrow {
        margin-left: auto;
    }

    .rotate {
        transform: rotate(90deg);
    }

    /* Submenu Default */
    .submenu {
        background: #f9fafb;
        overflow: hidden;
    }

    .submenu .menu-item {
        padding-left: 54px;
        font-size: 0.9rem;
    }

    /* =========================================
       3. LOGIKA MINIMIZE (DESKTOP)
    ========================================= */
    @media (min-width: 992px) {
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
        }

        /* KONDISI KECIL */
        body.sidebar-minimized .sidebar {
            width: 80px;
            overflow: visible !important;
        }

        body.sidebar-minimized .menu-text,
        body.sidebar-minimized .menu-arrow,
        body.sidebar-minimized .menu-header {
            display: none;
        }

        body.sidebar-minimized .menu-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        body.sidebar-minimized .icon-start {
            margin-right: 0;
        }

        /* Sembunyikan Submenu Standard saat kecil */
        body.sidebar-minimized .submenu {
            display: none;
        }

        /* MENU MELAYANG (FLYOUT) */
        body.sidebar-minimized .submenu:not(.d-none) {
            display: block !important;
            position: absolute;
            left: 80px;
            width: 220px;
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            margin-top: -50px;
            border-radius: 0 8px 8px 8px;
        }

        body.sidebar-minimized .submenu .menu-item {
            padding-left: 20px !important;
            justify-content: flex-start;
        }

        body.sidebar-minimized .submenu .menu-text {
            display: block !important;
            opacity: 1 !important;
        }

        .desktop-logo {
            display: flex !important;
        }

        .offcanvas-header {
            display: none;
        }
    }

    @media (max-width: 767.98px) {

    .sidebar {
        position: fixed;       /* jadi floating */
        top: 0;
        left: -100%;           /* DISIMPAN ke kiri */
        width: 80%;            /* lebar menu mobile */
        max-width: 300px;
        height: 100vh;
        z-index: 1050;
        transition: left 0.3s ease-in-out;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    }

    .sidebar.show {
        left: 0;               /* MUNCUL saat hamburger diklik */
    }
    }
</style>

<aside class="sidebar offcanvas-start" id="sidebarMenu" data-bs-scroll="true" data-bs-backdrop="false">
    <div class="offcanvas-header bg-primary text-white d-lg-none">
        <h5 class="offcanvas-title fw-bold">Menu</h5>
        <button type="button" class="btn-close btn-close-white" id="btnCloseSidebarMobile"></button>
    </div>

    <!-- Mobile Toggle Button (Visible only on mobile navbars usually, but kept here for structure) -->

    <!-- <div class="desktop-logo d-none d-lg-flex align-items-center justify-content-center py-4 border-bottom">
        <h5 class="fw-bold text-primary m-0"><i class="fas fa-archive me-2"></i>E-Arsip</h5>
    </div> -->


    <nav class="flex-grow-1 overflow-auto py-3 custom-scrollbar">
        <p class="px-3 text-uppercase small text-muted fw-bold mb-2 menu-header" style="font-size: 0.7rem;">Menu Utama
        </p>

        <a href="<?= $base_url ?>/dashboard.php" class="menu-item <?= $active('dashboard.php') ?>" title="Dashboard">
            <i class="fas fa-home icon-start"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        <button class="menu-item <?= $arsip_active ? 'active' : '' ?>" onclick="toggleDropdown('arsipDinamis')"
            title="Penyimpanan">
            <i class="fas fa-folder icon-start"></i>
            <span class="menu-text">Penyimpanan Arsip</span>
            <i id="icon-arsipDinamis"
                class="fas fa-chevron-right small menu-arrow <?= $arsip_active ? 'rotate' : '' ?>"></i>
        </button>

        <div id="arsipDinamis" class="submenu <?= $arsip_active ? '' : 'd-none' ?>">
            <a href="<?= $base_url ?>/arsip-vital.php" class="menu-item <?= $active('arsip-vital') ?>">
                <span class="menu-text">Arsip Vital</span>
            </a>
            <a href="<?= $base_url ?>/arsip-permanen.php" class="menu-item <?= $active('arsip-permanen') ?>">
                <span class="menu-text">Arsip Permanen</span>
            </a>
            <a href="<?= $base_url ?>/arsip-aktif.php" class="menu-item <?= $active('arsip-aktif') ?>">
                <span class="menu-text">Arsip Aktif</span>
            </a>
            <a href="<?= $base_url ?>/arsip-inaktif.php" class="menu-item <?= $active('arsip-inaktif') ?>">
                <span class="menu-text">Arsip Inaktif</span>
            </a>
        </div>

        <!-- Pengawasan removed because functionality does not exist in current app -->

        <button class="menu-item <?= $rekap_active ? 'active' : '' ?>" onclick="toggleDropdown('rekap')" title="Rekap">
            <i class="fas fa-list icon-start"></i>
            <span class="menu-text">Rekapitulasi</span>
            <i id="icon-rekap" class="fas fa-chevron-right small menu-arrow <?= $rekap_active ? 'rotate' : '' ?>"></i>
        </button>

        <div id="rekap" class="submenu <?= $rekap_active ? '' : 'd-none' ?>">
            <!-- Pointing to rekap.php which is the main recap file -->
            <a href="<?= $base_url ?>/rekap.php" class="menu-item <?= $active('rekap') ?>">
                <span class="menu-text">Rekap Peminjaman</span>
            </a>
        </div>

        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="border-top my-2 mx-3"></div>
            <a href="<?= $base_url ?>/user.php" class="menu-item <?= $active('user.php') ?>" title="Data User">
                <i class="fas fa-users icon-start"></i>
                <span class="menu-text">Data User</span>
            </a>
        <?php endif; ?>

        <div class="border-top my-3 mx-3"></div>

        <a href="<?= $base_url ?>/index.php?logout=true" class="menu-item menu-logout" title="Logout">
            <i class="fas fa-sign-out-alt icon-start text-danger"></i>
            <span class="menu-text text-danger">Logout</span>
        </a>

    </nav>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // We look for a btnToggleSidebar usually in header, but for now just the logic
        const toggleBtn = document.getElementById("btnToggleSidebar");
        const sidebar = document.getElementById("sidebarMenu");

        // --- 1. CEK LOCAL STORAGE SAAT LOAD ---
        const isMinimized = localStorage.getItem('sidebarState') === 'minimized';

        if (isMinimized && window.innerWidth >= 992) {
            document.body.classList.add("sidebar-minimized");
            document.querySelectorAll('.submenu').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.menu-arrow').forEach(el => el.classList.remove('rotate'));
        }

        // --- 2. LOGIKA KLIK TOMBOL HAMBURGER ---
        // Ensuring bootstrap is available
        if (typeof bootstrap !== 'undefined' && sidebar) {

            // Only init offcanvas if we are on mobile? Bootstrap handles data-bs attributes auto, but 
            // if we need manual control:
            const bsOffcanvas = new bootstrap.Offcanvas(sidebar);

            if (toggleBtn) {
                toggleBtn.addEventListener("click", function (e) {
                    e.preventDefault();

                    if (window.innerWidth >= 992) {
                        // Desktop Toggle
                        document.body.classList.toggle("sidebar-minimized");

                        if (document.body.classList.contains("sidebar-minimized")) {
                            localStorage.setItem('sidebarState', 'minimized');
                            document.querySelectorAll('.submenu').forEach(el => el.classList.add('d-none'));
                            document.querySelectorAll('.menu-arrow').forEach(el => el.classList.remove('rotate'));
                        } else {
                            localStorage.setItem('sidebarState', 'expanded');
                        }
                    } else {
                        // Mobile Toggle
                        bsOffcanvas.show();
                    }
                });
            }

            // Handle mobile close button specifically if needed
            const btnClose = document.getElementById("btnCloseSidebarMobile");
            if (btnClose) {
                btnClose.addEventListener('click', function () {
                    bsOffcanvas.hide();
                });
            }
        }
    });

    // --- 3. LOGIKA TOGGLE SUBMENU ---
    function toggleDropdown(id) {
        const menu = document.getElementById(id);
        const icon = document.getElementById("icon-" + id);

        if (document.body.classList.contains("sidebar-minimized")) {
            document.querySelectorAll('.submenu').forEach(el => {
                if (el.id !== id) el.classList.add('d-none');
            });
            document.querySelectorAll('.menu-arrow').forEach(el => {
                if (el.id !== "icon-" + id) el.classList.remove('rotate');
            });
        }

        if (menu) menu.classList.toggle("d-none");
        if (icon) icon.classList.toggle("rotate");
    }
</script>