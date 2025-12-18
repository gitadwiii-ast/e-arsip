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
    <main class="flex-grow-1 p-4" style="background-color:#f3f4f6; min-height:100vh; min-width: 0;">
        <div class="container-fluid">
            <div class="card shadow-lg rounded-3 overflow-hidden">


                <!-- HEADER -->
                <div class="card-header text-white d-flex justify-content-between align-items-center"
                    style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Inaktif
                    </h5>
                </div>

                <!-- BODY -->
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-4 align-items-center">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari berdasarkan uraian, nomor arsip, atau lokasi..."
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>
                                Cari</button>
                        </div>
                    </form>


                    <?php
                    // FILTER SEARCH
                    // FILTER SEARCH (ARSIP INAKTIF - FIX)
                    $where = "";
                    if (isset($_GET['search']) && $_GET['search'] !== '') {
                        $search = mysqli_real_escape_string($db, $_GET['search']);
                        $where = "WHERE 
                                        arsip_inaktif.uraian_arsip LIKE '%$search%'
                                        OR arsip_inaktif.nomor_arsip LIKE '%$search%'
                                        OR arsip_inaktif.keterangan LIKE '%$search%'";
                    }



                    $query_string = "
                                    SELECT arsip_inaktif.*, 
                                        kode_klasifikasi.kode_klasifikasi,
                                        kode_klasifikasi.deskripsi,
                                        sub_klasifikasi.nama_sub,
                                        sub_klasifikasi.id_sub,
                                        sub_sub_klasifikasi.nama_subsub,
                                        sub_sub_klasifikasi.id_subsub,
                                        tingkat_perkembangan.nama_tingkat,  /* <--- TAMBAHAN 1: Ambil kolom nama */
                                        peminjaman_arsip.id_peminjaman
                                    FROM arsip_inaktif
                                    LEFT JOIN kode_klasifikasi ON arsip_inaktif.id_kode = kode_klasifikasi.id_kode
                                    LEFT JOIN sub_klasifikasi ON arsip_inaktif.id_sub = sub_klasifikasi.id_sub
                                    LEFT JOIN sub_sub_klasifikasi ON arsip_inaktif.id_subsub = sub_sub_klasifikasi.id_subsub
                                    LEFT JOIN tingkat_perkembangan ON arsip_inaktif.id_tingkat = tingkat_perkembangan.id_tingkat /* <--- TAMBAHAN 2: Sambungkan tabelnya */
                                    LEFT JOIN peminjaman_arsip ON peminjaman_arsip.arsip_type = 'inaktif' 
                                        AND peminjaman_arsip.arsip_id = arsip_inaktif.id_arsip_inaktif 
                                        AND peminjaman_arsip.user_id = '$user_id'
                                        AND peminjaman_arsip.status = 'aktif'
                                        AND peminjaman_arsip.tanggal_expired > NOW()
                                    $where
                                    ORDER BY arsip_inaktif.id_arsip_inaktif DESC
                                ";

                    $query_arsip = mysqli_query($db, $query_string);
                    if (!$query_arsip) {
                        die("Query Error: " . mysqli_error($db));
                    }
                    ?>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="col-no">No</th>
                                    <th style="width: 200px;">Uraian Informasi Arsip</th>
                                    <th style="width: 120px;">No Arsip/Berkas</th>
                                    <th style="width: 150px;">Kode Klasifikasi</th>
                                    <th style="width: 150px;">Jenis/Series Arsip</th>
                                    <th style="width: 120px;">Tahun</th>
                                    <th style="width: 120px;">Retensi</th>
                                    <th style="width: 120px;">Jumlah</th>
                                    <th style="width: 200px;">Tingkat Perkembangan</th>
                                    <th style="width: 150px;">Keterangan</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                if (mysqli_num_rows($query_arsip) > 0) { ?>
                                    <?php while ($data = mysqli_fetch_assoc($query_arsip)) { ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>

                                            <td><?= htmlspecialchars($data['uraian_arsip'] ?? '') ?></td>

                                            <td class="text-center fw-bold"><?= htmlspecialchars($data['nomor_arsip'] ?? '') ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($data['nama_subsub'])) { ?>
                                                    <div class="fw-bold text-primary"><?= $data['id_subsub'] ?? '' ?></div>
                                                    <div class="text-muted"><?= $data['nama_subsub'] ?? '' ?></div>
                                                <?php } elseif (!empty($data['nama_sub'])) { ?>
                                                    <div class="fw-bold text-primary"><?= $data['id_sub'] ?? '' ?></div>
                                                    <div class="text-muted"><?= $data['nama_sub'] ?? '' ?></div>
                                                <?php } else { ?>
                                                    <div class="fw-bold text-primary"><?= $data['kode_klasifikasi'] ?? '' ?></div>
                                                    <div class="text-muted"><?= $data['deskripsi'] ?? '' ?></div>
                                                <?php } ?>
                                            </td>

                                            <td><?= htmlspecialchars($data['id_jenis'] ?? '') ?></td>

                                            <td class="text-center"><?= htmlspecialchars($data['kurun_waktu'] ?? '') ?></td>

                                            <td class="text-center"><?= htmlspecialchars($data['retensi'] ?? '') ?></td>

                                            <td class="text-center"><?= htmlspecialchars($data['jumlah'] ?? '') ?></td>

                                            <td class="text-center">
                                                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 ">
                                                    <?= htmlspecialchars($data['nama_tingkat'] ?? $data['id_tingkat'] ?? '-') ?>
                                                </span>
                                            </td>

                                            <td><?= htmlspecialchars($data['keterangan'] ?? '') ?></td>

                                            <td class="text-center">
                                                <?php if (!empty($data['id_peminjaman'])): ?>
                                                    <a href="akses_file.php?type=inaktif&id=<?= $data['id_arsip_inaktif'] ?>"
                                                        target="_blank" class="btn btn-success btn-sm">
                                                        <i class="fas fa-file-pdf me-1"></i> Lihat
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" onclick="openBorrowModal(
                                                    '<?= $data['id_arsip_inaktif'] ?>',
                                                    'inaktif',
                                                    '<?= addslashes($data['uraian_informasi'] ?? '') ?>',
                                                    '<?= addslashes($data['kode_klasifikasi'] ?? '') ?>',
                                                    '<?= $data['id_kode'] ?? '' ?>',
                                                    '<?= $data['id_sub'] ?? '' ?>',
                                                    '<?= $data['id_subsub'] ?? '' ?>'
                                                )" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-hand-holding mr-2"></i> Pinjam
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
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
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php include 'footer.php'; ?>

        </div>
    </main>