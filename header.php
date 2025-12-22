<?php
// 1. LOGIKA: Cek Halaman untuk Menampilkan Search Bar
$file_sekarang = basename($_SERVER['PHP_SELF']);
$halaman_arsip = [
    'dashboard.php',
    'arsip-aktif.php',
    'arsip-inaktif.php',
    'arsip-permanen.php',
    'arsip-vital.php',
    'arsip-fisik.php',
    'rekap.php'
];
$tampil_search = in_array($file_sekarang, $halaman_arsip);

// 2. Logic Kategori Aktif untuk Dropdown
$kategori_aktif = 'semua';
if ($file_sekarang == 'arsip-aktif.php')
    $kategori_aktif = 'aktif';
elseif ($file_sekarang == 'arsip-inaktif.php')
    $kategori_aktif = 'inaktif';
elseif ($file_sekarang == 'arsip-vital.php')
    $kategori_aktif = 'vital';
elseif ($file_sekarang == 'arsip-permanen.php')
    $kategori_aktif = 'permanen';
elseif ($file_sekarang == 'arsip-fisik.php')
    $kategori_aktif = 'fisik';

$current_search = htmlspecialchars($_GET['search'] ?? '');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Arsip Digital</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        table th,
        table td {
            white-space: normal;
            word-wrap: break-word;
            vertical-align: middle;
        }

        table {
            width: 100%;
            table-layout: fixed;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74a3b, #be2617);
        }

        .search-group {
            background-color: #f3f4f6;
            /* Abu-abu muda */
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            /* Sudut tumpul */
            display: flex;
            align-items: center;
            width: 100%;
            max-width: 600px;
            padding: 2px;
            transition: all 0.2s;
        }

        .search-group:focus-within {
            background-color: #fff;
            border-color: #4f46e5;
            /* Biru saat diklik */
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-select {
            background-color: transparent;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: #4b5563;
            padding-left: 10px;
            border-right: 1px solid #d1d5db;
            max-width: 150px;
            cursor: pointer;
        }

        .search-select:focus {
            box-shadow: none;
        }

        .search-input {
            background-color: transparent;
            border: none;
            padding: 6px 12px;
            width: 100%;
            color: #1f2937;
        }

        .search-input:focus {
            background-color: transparent;
            box-shadow: none;
        }

        .search-btn {
            border: none;
            background: transparent;
            color: #6b7280;
            padding: 0 15px;
        }

        .search-btn:hover {
            color: #4f46e5;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-white sticky-top shadow-sm">
        <div class="container-fluid">

            <div class="d-flex align-items-center">

                <button class="btn btn-light text-secondary me-3 border-0" type="button" id="btnToggleSidebar">
                    <i class="fas fa-bars fa-lg"></i>
                </button>

                <a class="navbar-brand fw-bold text-primary tracking-wide fs-5" href="#">
                    <i class="fas fa-archive me-2"></i>E-ARSIP
                </a>
            </div>

            <?php if ($tampil_search): ?>
                <div class="mx-auto flex-grow-1 d-flex justify-content-center px-3 d-none d-lg-flex">
                    <form id="globalSearchForm" method="GET" class="search-group">

                        <select id="kategoriArsip" class="form-select search-select">
                            <option value="" disabled selected hidden>Kategori</option>

                            <?php if ($kategori_aktif !== 'semua'): ?>
                                <option value="semua">Semua</option>
                            <?php endif; ?>

                            <?php if ($kategori_aktif !== 'aktif'): ?>
                                <option value="aktif">Arsip Aktif</option>
                            <?php endif; ?>

                            <?php if ($kategori_aktif !== 'inaktif'): ?>
                                <option value="inaktif">Arsip Inaktif</option>
                            <?php endif; ?>

                            <?php if ($kategori_aktif !== 'vital'): ?>
                                <option value="vital">Arsip Vital</option>
                            <?php endif; ?>

                            <?php if ($kategori_aktif !== 'permanen'): ?>
                                <option value="permanen">Arsip Permanen</option>
                            <?php endif; ?>

                            <?php if ($kategori_aktif !== 'fisik'): ?>
                                <option value="fisik">Arsip Fisik</option>
                            <?php endif; ?>
                        </select>

                        <input type="text" name="search" class="form-control search-input"
                            placeholder="Cari arsip disini..." value="<?= $current_search ?>">

                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="dropdown ms-auto">
                <a href="#"
                    class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle p-1 rounded hover-bg-light"
                    id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-none d-md-block text-end me-2" style="line-height: 1.2;">
                        <span class="d-block fw-bold small"><?= $_SESSION['nama_lengkap'] ?? 'User' ?></span>
                        <span class="d-block text-muted" style="font-size: 0.7rem;">Admin</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['nama_lengkap'] ?? 'User' ?>&background=0D8ABC&color=fff"
                        alt="" width="38" height="38" class="rounded-circle">
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item text-danger" href="index.php?logout=true"><i
                                class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                </ul>
            </div>

        </div>
    </nav>

    <?php if ($tampil_search): ?>
        <div class="d-lg-none bg-white p-2 border-bottom">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control bg-light border-start-0"
                    placeholder="Gunakan Laptop untuk filter lengkap..." disabled>
            </div>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById('globalSearchForm');
            const select = document.getElementById('kategoriArsip');

            if (form && select) {
                // GANTI INI SESUAI FOLDER PROYEK ANDA
                const baseURL = "/peminjaman-arsip";

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const keyword = form.querySelector('input[name="search"]').value;
                    const kategori = select.value;

                    // Ambil URL halaman saat ini untuk default (jika user tidak memilih kategori)
                    let currentPath = window.location.pathname;
                    let targetURL = "";

                    if (kategori === "") {
                        // Jika user tidak memilih kategori (tetap di tulisan "Kategori")
                        // Maka cari di halaman ini saja
                        targetURL = currentPath;
                    } else {
                        // Jika user memilih kategori lain
                        // CORRECTED PATHS: Removed subdirectories 'arsip/arsip-X/'
                        switch (kategori) {
                            case 'aktif': targetURL = baseURL + "/arsip-aktif.php"; break;
                            case 'inaktif': targetURL = baseURL + "/arsip-inaktif.php"; break;
                            case 'vital': targetURL = baseURL + "/arsip-vital.php"; break;
                            case 'permanen': targetURL = baseURL + "/arsip-permanen.php"; break;
                            case 'fisik': targetURL = baseURL + "/arsip-fisik.php"; break;
                            case 'semua':
                                // Asumsi ada halaman dashboard atau pencarian global
                                targetURL = "/peminjaman-arsip/dashboard.php";
                                break;
                            default: targetURL = baseURL + "/arsip-aktif.php"; break;
                        }
                    }

                    // Redirect
                    window.location.href = targetURL + "?search=" + encodeURIComponent(keyword);
                });
            }
        });
    </script>