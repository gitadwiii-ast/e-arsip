<?php
session_start();
include 'konektor.php';

// Cek session
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    exit();
}

// 1. QUERY JUMLAH ARSIP
$count_vital = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_vital FROM arsip_vital"));
$count_permanen = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_permanen FROM arsip_permanen"));
$count_aktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_aktif FROM arsip_aktif"));
$count_inaktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_inaktif FROM arsip_inaktif"));

// 2. QUERY DAFTAR PEMINJAMAN YANG SEDANG AKTIF
$query_pinjam = "SELECT * FROM peminjaman_arsip 
                 WHERE status = 'aktif' AND tanggal_expired > NOW()
                 ORDER BY id_peminjaman DESC";
$result_pinjam = mysqli_query($db, $query_pinjam);

include 'header.php';
?>

<div class="d-flex">

    <!-- INCLUDE SIDEBAR -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-grow-1 p-4" style="min-height: 100vh; overflow-x: hidden;">
        <div class="container-fluid">

            <!-- WELCOME HEADER -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Dashboard</h1>
                <span class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-calendar me-2"></i> <?= date('d M Y'); ?>
                </span>
            </div>

            <!-- ALERT WELCOME -->
            <div class="alert alert-primary shadow-sm border-0 d-flex align-items-center" role="alert">
                <i class="fas fa-user-circle fa-2x me-3"></i>
                <div>
                    Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>!
                    <div class="small">Anda login sebagai <?= ucfirst($_SESSION['role']); ?></div>
                </div>
            </div>

            <!-- CARDS ROW -->
            <div class="row g-4 mb-4">

                <!-- CARD VITAL -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-counter bg-gradient-danger text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs fw-bold text-uppercase mb-1">Arsip Vital</div>
                                    <div class="h2 mb-0 fw-bold"><?= $count_vital; ?></div>
                                </div>
                                <i class="fas fa-heartbeat fa-3x text-white-50"></i>
                            </div>
                        </div>
                        <a href="arsip-vital.php" class="card-footer text-white clearfix small z-1"
                            style="background: rgba(0,0,0,0.1); text-decoration: none;">
                            <span class="float-start">Lihat Detail</span>
                            <span class="float-end"><i class="fas fa-angle-right"></i></span>
                        </a>
                    </div>
                </div>

                <!-- CARD PERMANEN -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-counter bg-gradient-primary text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs fw-bold text-uppercase mb-1">Arsip Permanen</div>
                                    <div class="h2 mb-0 fw-bold"><?= $count_permanen; ?></div>
                                </div>
                                <i class="fas fa-archive fa-3x text-white-50"></i>
                            </div>
                        </div>
                        <a href="arsip-permanen.php" class="card-footer text-white clearfix small z-1"
                            style="background: rgba(0,0,0,0.1); text-decoration: none;">
                            <span class="float-start">Lihat Detail</span>
                            <span class="float-end"><i class="fas fa-angle-right"></i></span>
                        </a>
                    </div>
                </div>

                <!-- CARD AKTIF -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-counter bg-gradient-success text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs fw-bold text-uppercase mb-1">Arsip Aktif</div>
                                    <div class="h2 mb-0 fw-bold"><?= $count_aktif; ?></div>
                                </div>
                                <i class="fas fa-check-circle fa-3x text-white-50"></i>
                            </div>
                        </div>
                        <a href="arsip-aktif.php" class="card-footer text-white clearfix small z-1"
                            style="background: rgba(0,0,0,0.1); text-decoration: none;">
                            <span class="float-start">Lihat Detail</span>
                            <span class="float-end"><i class="fas fa-angle-right"></i></span>
                        </a>
                    </div>
                </div>

                <!-- CARD INAKTIF -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-counter bg-gradient-warning text-white h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-xs fw-bold text-uppercase mb-1">Arsip Inaktif</div>
                                    <div class="h2 mb-0 fw-bold"><?= $count_inaktif; ?></div>
                                </div>
                                <i class="fas fa-box-open fa-3x text-white-50"></i>
                            </div>
                        </div>
                        <a href="arsip-inaktif.php" class="card-footer text-white clearfix small z-1"
                            style="background: rgba(0,0,0,0.1); text-decoration: none;">
                            <span class="float-start">Lihat Detail</span>
                            <span class="float-end"><i class="fas fa-angle-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- TABLE PEMINJAMAN AKTIF -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-clock me-2"></i> Peminjaman Sedang Aktif
                    </h6>
                    <span class="badge bg-primary rounded-pill"><?= mysqli_num_rows($result_pinjam); ?> Item</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Peminjam</th>
                                    <th>Arsip</th>
                                    <th>Uraian Informasi</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Sisa Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result_pinjam) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result_pinjam)): ?>

                                        <!-- Perhitungan sisa waktu -->
                                        <?php
                                        $now = new DateTime();
                                        $expired = new DateTime($row['tanggal_expired']);
                                        $interval = $now->diff($expired);

                                        // Format sisa waktu
                                        if ($now > $expired) {
                                            $sisa_waktu = "<span class='badge bg-danger'>Expired</span>";
                                        } else {
                                            $sisa_waktu = "<span class='badge bg-success'>" . $interval->days . " Hari " . $interval->h . " Jam</span>";
                                        }
                                        ?>

                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($row['nama_peminjam']); ?></strong><br>
                                                <small
                                                    class="text-muted"><?= htmlspecialchars($row['instansi_peminjam']); ?></small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-secondary text-uppercase"><?= $row['arsip_type']; ?></span>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['uraian_informasi']); ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td class="fw-bold text-danger">
                                                <?= date('d/m/Y H:i', strtotime($row['tanggal_expired'])); ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $sisa_waktu; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-3"></i><br>
                                            Tidak ada peminjaman yang sedang aktif
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- INCLUDE FOOTER -->
    <?php include 'footer.php'; ?>

    <style>
        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74a3b, #be2617);
        }
    </style>