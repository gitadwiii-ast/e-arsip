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

            <div class="card shadow-lg rounded-3">

                <!-- HEADER -->
                <div class="card-header text-white" style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Vital
                    </h5>
                </div>

                <!-- BODY -->
                <div class="card-body">

                    <!-- SEARCH -->
                    <form method="GET" class="row g-2 mb-4">
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
                    // FILTER SEARCH
                    $where = "";
                    if (!empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($db, $_GET['search']);
                        $where = "WHERE av.uraian_informasi LIKE '%$search%'
                                  OR av.nomor_arsip LIKE '%$search%'
                                  OR av.lokasi_simpan LIKE '%$search%'";
                    }

                    // QUERY DATABASE
                    $query = "
                        SELECT av.*,
                            ja.nama_jenis AS jenis_arsip,
                            kk.kode_klasifikasi,
                            kk.deskripsi AS deskripsi_kode,
                            p.id_peminjaman
                        FROM arsip_vital av
                        LEFT JOIN kode_klasifikasi kk
                            ON av.id_kode = kk.id_kode
                        LEFT JOIN jenis_arsip ja
                            ON av.id_jenis = ja.id_jenis
                        LEFT JOIN peminjaman_arsip p
                            ON p.arsip_type = 'vital'
                            AND p.arsip_id = av.id_arsip_vital
                            AND p.user_id = '$user_id'
                            AND p.status = 'aktif'
                            AND p.tanggal_expired > NOW()
                        $where
                        ORDER BY av.id_arsip_vital DESC
                    ";


                    $result = mysqli_query($db, $query);
                    $no = 1;
                    ?>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th class="col-no">No</th>
                                    <th style="width: 200px;">Uraian Informasi</th>
                                    <th style="width: 100px;">Asal</th>
                                    <th style="width: 150px;">Klasifikasi</th>
                                    <th style="width: 200px;">Jenis Arsip</th>
                                    <th style="width: 150px;">Nomor Arsip</th>
                                    <th style="width: 100px;">Retensi</th>
                                    <th style="width: 200px;">Lokasi</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <?php $dipinjam = !empty($row['id_peminjaman']); ?>
                                        <tr>
                                            <td class="col-no"><?= $no++; ?></td>

                                            <td>
                                                <?= htmlspecialchars($row['uraian_informasi']); ?>
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary badge-asal">
                                                    <?= ucfirst($row['asal_arsip']); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <strong><?= $row['kode_klasifikasi']; ?></strong><br>
                                                <small class="text-muted"><?= $row['deskripsi_kode']; ?></small>
                                            </td>

                                            <td><?= htmlspecialchars($row['jenis_arsip']); ?></td>

                                            <td class="col-nomor"><?= $row['nomor_arsip']; ?></td>

                                            <td class="text-center"><?= $row['retensi']; ?> Th</td>

                                            <td><?= $row['lokasi_simpan']; ?></td>

                                            <td class="col-aksi text-center">
                                                <?php if ($dipinjam && !empty($row['file_pdf'])): ?>
                                                    <!-- Jika arsip sudah dipinjam, buka file dari folder uploads -->
                                                    <a href="upload/<?= htmlspecialchars($row['file_pdf']); ?>" target="_blank"
                                                        class="btn btn-success btn-sm" title="Lihat File PDF">
                                                        <i class="bi bi-file-earmark-pdf"></i> Lihat
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Jika belum dipinjam, tampilkan tombol Pinjam -->
                                                    <button type="button" onclick="openBorrowModal(
                                                            '<?= $row['id_arsip_vital']; ?>',
                                                            'vital',
                                                            '<?= addslashes($row['uraian_informasi']); ?>',
                                                            '<?= addslashes($row['kode_klasifikasi']); ?>',
                                                            '<?= $row['id_kode']; ?>',
                                                            '<?= $row['id_sub'] ?? ''; ?>',
                                                            '<?= $row['id_subsub'] ?? ''; ?>'
                                                        )" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-hand-holding mr-2"></i> Pinjam
                                                    </button>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9"
                                            class="px-4 py-8 border-b border-gray-200 bg-white text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                            <p class="text-lg">Tidak ada data arsip permanen</p>
                                            <?php if (isset($_GET['search']) && $_GET['search'] != '') { ?>
                                                <p class="text-sm mt-2">Coba kata kunci pencarian yang berbeda</p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div> <!-- card-body -->
            </div> <!-- card -->

        </div> <!-- container-fluid -->
    </main>
    <?php include 'footer.php'; ?>
</div>