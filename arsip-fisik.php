<?php
session_start();
include 'konektor.php';
$user_id = $_SESSION['id'] ?? null;
include 'header.php';
?>

<style>
    /* Container tabel */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Jangan paksa tabel mengecil */
    table {
        min-width: 1000px;
        /* bebas, bisa 900–1200 */
    }

    /* Umum */
    th,
    td {
        vertical-align: middle;
        word-wrap: break-word;
        font-size: 14px;
    }

    /* Kolom khusus */
    .col-no {
        width: 45px;
        text-align: center;
        white-space: nowrap;
    }

    .col-nomor {
        width: 140px;
        font-family: monospace;
        white-space: nowrap;
    }

    .col-aksi {
        width: 120px;
        text-align: center;
        white-space: nowrap;
    }
</style>


<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <main class="flex-grow-1 p-4" style="background-color:#f3f4f6; min-height:100vh; min-width:0;">
        <div class="container-fluid">

            <div class="card shadow-lg rounded-3 overflow-hidden">

                <!-- HEADER -->
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Fisik
                    </h5>
                </div>

                <!-- BODY -->
                <div class="card-body">

                    <!-- SEARCH -->
                    <form method="GET" class="row g-2 mb-4 align-items-center">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari berdasarkan uraian, nomor arsip, atau lokasi..."
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                        </div>
                    </form>

                    <?php
                    $where = "";
                    if (!empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($db, $_GET['search']);
                        $where = "WHERE aa.uraian_informasi LIKE '%$search%'
                                  OR aa.no_berkas LIKE '%$search%'
                                  OR aa.kurun_waktu LIKE '%$search%'";
                    }

                    // NOTE: Assumes table name is 'arsip_fisik' and primary key is 'id_arsip_fisik'
                    $query = "SELECT aa.*,
                                     kk.kode_klasifikasi,
                                     kk.deskripsi AS deskripsi_kode,
                                     sk.nama_sub,
                                     ssk.nama_subsub,
                                     kkm.nama_keamanan,
                                     p.id_peminjaman,
                                     p.tanggal_expired
                              FROM arsip_fisik aa
                              LEFT JOIN kode_klasifikasi kk ON aa.id_kode = kk.id_kode
                              LEFT JOIN sub_klasifikasi sk ON aa.id_sub = sk.id_sub
                              LEFT JOIN sub_sub_klasifikasi ssk ON aa.id_subsub = ssk.id_subsub
                              LEFT JOIN klasifikasi_keamanan kkm ON aa.id_keamanan = kkm.id_keamanan
                              LEFT JOIN peminjaman_arsip p
                                ON p.arsip_type = 'fisik'
                                AND p.arsip_id = aa.id_arsip_fisik
                                AND p.user_id = '$user_id'
                                AND p.status = 'aktif'
                                AND p.tanggal_expired > NOW()
                              $where
                              ORDER BY aa.id_arsip_fisik DESC";

                    $arsip = mysqli_query($db, $query);
                    $no = 1;
                    ?>

                    <!-- TABLE -->
                    <div class="table-responsive table-scroll">
                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="col-no">No</th>
                                    <th style="width: 250px;">Uraian Informasi</th>
                                    <th style="width: 200px;">Uraian Info Berkas</th>
                                    <th style="width: 150px;">Klasifikasi</th>
                                    <th style="width: 120px;">Nomor Berkas</th>
                                    <th style="width: 80px;">Jumlah</th>
                                    <th style="width: 100px;">Kurun Waktu</th>
                                    <th style="width: 120px;">Keamanan</th>
                                    <th class="col-action" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($arsip && mysqli_num_rows($arsip) > 0): ?>
                                    <?php while ($p = mysqli_fetch_assoc($arsip)):
                                        $has_borrowing = !empty($p['id_peminjaman']);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>

                                            <td>
                                                <div class="text-limit">
                                                    <?= htmlspecialchars($p['uraian_informasi']); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="text-limit">
                                                    <?= htmlspecialchars($p['uraian_informasi_berkas'] ?? '-'); ?>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-primary">
                                                    <?= htmlspecialchars($p['kode_klasifikasi']); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($p['deskripsi_kode']); ?>
                                                    <?php if (!empty($p['nama_sub'])): ?>
                                                        <br><i class="fas fa-angle-right text-xs"></i>
                                                        <?= htmlspecialchars($p['nama_sub']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>

                                            <td class="font-monospace text-center">
                                                <?= htmlspecialchars($p['no_berkas'] ?? '-'); ?>
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($p['jumlah'] ?? '0'); ?> berkas
                                            </td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($p['kurun_waktu'] ?? '-'); ?>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                                    <?= htmlspecialchars($p['nama_keamanan'] ?? '-'); ?>
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                <?php if (!empty($p['id_peminjaman'])) { ?>
                                                    <a href="akses_file.php?type=fisik&id=<?= $p['id_arsip_fisik']; ?>"
                                                        class="btn btn-success btn-sm" target="_blank">
                                                        Lihat
                                                    </a>
                                                <?php } else { ?>
                                                    <button class="btn btn-primary btn-sm" onclick="openBorrowModal(
                                                        '<?= $p['id_arsip_fisik']; ?>',
                                                        'fisik',
                                                        '<?= addslashes($p['uraian_informasi']); ?>',
                                                        '<?= addslashes($p['kode_klasifikasi']); ?>',
                                                        '<?= $p['id_kode']; ?>',
                                                        '<?= $p['id_sub'] ?? ''; ?>',
                                                        '<?= $p['id_subsub'] ?? ''; ?>'
                                                    )"><i class="fas fa-hand-holding mr-2"></i>
                                                        Pinjam
                                                    </button>
                                                <?php } ?>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9"
                                            class="px-4 py-8 border-b border-gray-200 bg-white text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-lg">Tidak ada data arsip fisik</p>
                                            <?php if (isset($_GET['search']) && $_GET['search'] != '') { ?>
                                                <p class="text-sm mt-2">Coba kata kunci pencarian yang berbeda</p>
                                            <?php } ?>
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
    <?php include 'footer.php'; ?>
</div>