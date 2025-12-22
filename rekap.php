<?php
session_start();
include 'konektor.php';

// Cek session
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    exit();
}

$user_id = $_SESSION['id'];
date_default_timezone_set('Asia/Jakarta');

// 1. QUERY JUMLAH ARSIP UNTUK STATISTIK
$count_vital = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_vital FROM arsip_vital"));
$count_permanen = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_permanen FROM arsip_permanen"));
$count_aktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_aktif FROM arsip_aktif"));
$count_inaktif = mysqli_num_rows(mysqli_query($db, "SELECT id_arsip_inaktif FROM arsip_inaktif"));

// 2. QUERY RIWAYAT PEMINJAMAN (SEMUA)
$query_rekap = "SELECT * FROM peminjaman_arsip ORDER BY id_peminjaman DESC";
$result_rekap = mysqli_query($db, $query_rekap);

// HANDLE EXPORT EXCEL
if (isset($_GET['action']) && $_GET['action'] == 'export_excel') {
    // Note: Content-Type application/vnd-ms-excel with HTML content works best with .xls extension.
    // .xlsx requires actual ZIP-XML format which we are not generating here.
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Rekap_Peminjaman_" . date('Y-m-d') . ".xls");
    ?>
    <html>

    <body>
        <center>
            <h3>REKAPITULASI PEMINJAMAN ARSIP</h3>
            <p>Dicetak pada: <?= date("d/m/Y H:i:s") ?></p>
        </center>
        <table border="1">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Instansi</th>
                    <th>Tipe Arsip</th>
                    <th>Uraian Arsip</th>
                    <th>Tanggal Pinjam</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($result_rekap) > 0) {
                    // Reset pointer result_rekap karena sudah dipakai di bawah (tapi ini page load baru, jadi aman)
                    // Tapi karena ini di atas include header, variabel $result_rekap sudah ada.
                    while ($row = mysqli_fetch_assoc($result_rekap)) {
                        $expired = strtotime($row['tanggal_expired']) < time();

                        $status_style = "";
                        $date_style = "";

                        if ($row['status'] == 'selesai') {
                            $status_text = 'Kembali';
                            $status_style = 'background-color: #6c757d; color: white; text-align: center;';
                        } elseif ($expired) {
                            $status_text = 'Jatuh Tempo';
                            $status_style = 'background-color: #dc3545; color: white; text-align: center;';
                            $date_style = 'color: #dc3545; font-weight: bold;';
                        } else {
                            $status_text = 'Dipinjam';
                            $status_style = 'background-color: #198754; color: white; text-align: center;';
                        }
                        ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                            <td><?= htmlspecialchars($row['instansi_peminjam']) ?></td>
                            <td><?= strtoupper($row['arsip_type']) ?></td>
                            <td><?= htmlspecialchars($row['uraian_informasi']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pinjam'])) ?></td>
                            <td style="<?= $date_style ?>"><?= date('d/m/Y H:i', strtotime($row['tanggal_expired'])) ?></td>
                            <td style="<?= $status_style ?>"><?= $status_text ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="8" align="center">Belum ada riwayat peminjaman.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </body>

    </html>
    <?php
    exit(); // Stop execution to prevent HTML rendering
}

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
                <div class="d-print-none">
                    <a href="rekap.php?action=export_excel" target="_blank" class="btn btn-success shadow-sm me-2">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                    <button onclick="downloadPDF()" class="btn btn-danger shadow-sm me-2">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                    <button onclick="window.print()" class="btn btn-dark shadow-sm">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
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
            <div id="rekapTable" class="card shadow-sm border-0 rounded-3 overflow-hidden">
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

                                        if ($row['status'] == 'selesai') {
                                            $status_text = 'Kembali';
                                            $status_class = 'bg-secondary';
                                        } elseif ($expired) {
                                            $status_text = 'Jatuh Tempo';
                                            $status_class = 'bg-danger';
                                        } else {
                                            $status_text = 'Dipinjam';
                                            $status_class = 'bg-success';
                                        }
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

<!-- Libs for PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPDF() {
        const element = document.getElementById('rekapTable'); // Target the specific table card
        const opt = {
            margin: 1,
            filename: 'Rekap_Peminjaman_' + new Date().toISOString().slice(0, 10) + '.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(element).save();
    }
</script>

<?php include 'footer.php'; ?>