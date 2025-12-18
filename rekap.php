<?php
session_start();
include 'konektor.php';

// Cek session
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    exit();
}

$user_id = $_SESSION['id'];

// 1. QUERY JUMLAH ARSIP UNTUK STATISTIK
$count_vital = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_vital FROM arsip_vital"));
$count_permanen = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_permanen FROM arsip_permanen"));
$count_aktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_aktif FROM arsip_aktif"));
$count_inaktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_inaktif FROM arsip_inaktif"));

// 2. QUERY RIWAYAT PEMINJAMAN (SEMUA)
$query_rekap = "SELECT * FROM peminjaman_arsip ORDER BY id_peminjaman DESC";
$result_rekap = mysqli_query($db, $query_rekap);

include 'header.php';
?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <main class="flex-grow-1 p-4" style="background-color:#f3f4f6; min-height:100vh;">
        <div class="container-fluid">

            <!-- HEADER & PRINT BUTTON -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="fas fa-file-invoice me-2"></i> Rekapitulasi Peminjaman
                </h4>
                <button onclick="window.print()" class="btn btn-dark shadow-sm d-print-none">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </button>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="row g-3 mb-4 d-print-none">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-primary text-white">
                        <div class="card-body p-3">
                            <small class="text-white-50 text-uppercase fw-bold">Arsip Vital</small>
                            <h2 class="mb-0 mt-1 fw-bold"><?= $count_vital ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-success text-white">
                        <div class="card-body p-3">
                            <small class="text-white-50 text-uppercase fw-bold">Arsip Permanen</small>
                            <h2 class="mb-0 mt-1 fw-bold"><?= $count_permanen ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-info text-white">
                        <div class="card-body p-3">
                            <small class="text-white-50 text-uppercase fw-bold">Arsip Aktif</small>
                            <h2 class="mb-0 mt-1 fw-bold"><?= $count_aktif ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 bg-warning text-white">
                        <div class="card-body p-3">
                            <small class="text-white-50 text-uppercase fw-bold">Arsip Inaktif</small>
                            <h2 class="mb-0 mt-1 fw-bold"><?= $count_inaktif ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE REKAP -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="px-4 py-3" width="70">No</th>
                                    <th class="py-3">Peminjam</th>
                                    <th class="py-3">Tipe</th>
                                    <th class="py-3">Uraian Arsip</th>
                                    <th class="py-3">Tgl Pinjam</th>
                                    <th class="py-3">Jatuh Tempo</th>
                                    <th class="py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($result_rekap) > 0):
                                    while ($row = mysqli_fetch_assoc($result_rekap)):
                                        $expired = strtotime($row['tanggal_expired']) < time();
                                        $status_text = ($row['status'] == 'aktif' && !$expired) ? 'Aktif' : 'Selesai';
                                        $status_class = ($row['status'] == 'aktif' && !$expired) ? 'bg-success' : 'bg-secondary';
                                        ?>
                                        <tr>
                                            <td class="px-4 text-center text-muted"><?= $no++ ?></td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($row['nama_peminjam']) ?></div>
                                                <small
                                                    class="text-muted"><?= htmlspecialchars($row['instansi_peminjam']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border text-uppercase"
                                                    style="font-size: 10px;">
                                                    <?= $row['arsip_type'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;"
                                                    title="<?= htmlspecialchars($row['uraian_informasi']) ?>">
                                                    <?= htmlspecialchars($row['uraian_informasi']) ?>
                                                </div>
                                            </td>
                                            <td class="small"><?= date('d/m/Y H:i', strtotime($row['tanggal_pinjam'])) ?></td>
                                            <td class="small fw-bold <?= $expired ? 'text-danger' : '' ?>">
                                                <?= date('d/m/Y H:i', strtotime($row['tanggal_expired'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $status_class ?> px-3 rounded-pill">
                                                    <?= $status_text ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">Belum ada riwayat peminjaman.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-muted mt-3 small d-none d-print-block">
                Dicetak pada: <?= date('d/m/Y H:i:s') ?>
            </p>

        </div>
    </main>
</div>

<style>
    @media print {
        body {
            background: white !important;
        }

        .sidebar {
            display: none !important;
        }

        main {
            padding: 0 !important;
            width: 100% !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .table-responsive {
            overflow: visible !important;
        }
    }
</style>

<?php include 'footer.php'; ?>