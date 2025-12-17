<?php include 'header.php'; ?>

<style>
/* Tabel konsisten */
table {
    width: 100%;
    table-layout: fixed;
}

/* Header tabel */
table thead th {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    vertical-align: middle;
    padding: 12px 10px;
}

/* Isi tabel */
table td {
    font-size: 0.875rem;
    vertical-align: middle;
    padding: 10px;
    white-space: normal;
    word-break: break-word;
}

/* Batasi teks panjang */
.text-limit {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Kolom angka rapi */
.text-center {
    text-align: center;
}

/* Kolom aksi */
.col-action {
    width: 130px;
    white-space: nowrap;
}

/* Badge lebih kalem */
.badge {
    font-weight: 500;
    font-size: 0.75rem;
}
</style>


<div class="container-fluid mt-4">

    <div class="card shadow-sm border-0">

        <!-- HEADER -->
        <div class="card-header text-white"
             style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-archive me-2"></i> Data Arsip Aktif
            </h5>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <!-- SEARCH -->
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-10">
                    <input type="text"
                           name="search"
                           class="form-control"
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
            $user_id = $_SESSION['id'] ?? null;

            $where = "";
            if (!empty($_GET['search'])) {
                $search = mysqli_real_escape_string($db, $_GET['search']);
                $where = "WHERE aa.uraian_informasi LIKE '%$search%'
                          OR aa.no_berkas LIKE '%$search%'
                          OR aa.kurun_waktu LIKE '%$search%'";
            }

            $query = "SELECT aa.*,
                             kk.kode_klasifikasi,
                             kk.deskripsi AS deskripsi_kode,
                             kkm.nama_keamanan,
                             p.id_peminjaman,
                             p.tanggal_expired
                      FROM arsip_aktif aa
                      LEFT JOIN kode_klasifikasi kk ON aa.id_kode = kk.id_kode
                      LEFT JOIN klasifikasi_keamanan kkm ON aa.id_keamanan = kkm.id_keamanan
                      LEFT JOIN peminjaman_arsip p
                        ON p.arsip_type = 'aktif'
                        AND p.arsip_id = aa.id_arsip_aktif
                        AND p.user_id = '$user_id'
                        AND p.status = 'aktif'
                        AND p.tanggal_expired > NOW()
                      $where
                      ORDER BY aa.id_arsip_aktif DESC";

            $arsip = mysqli_query($db, $query);
            $no = 1;
            ?>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Uraian Informasi</th>
                            <th>Uraian Info Berkas</th>
                            <th>Klasifikasi</th>
                            <th>Nomor Berkas</th>
                            <th>Jumlah</th>
                            <th>Kurun Waktu</th>
                            <th>Keamanan</th>
                            <th class="col-action">Aksi</th>
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
                                    </small>
                                </td>

                                <td class="font-monospace">
                                    <?= htmlspecialchars($p['no_berkas'] ?? '-'); ?>
                                </td>

                                <td class="text-center">
                                    <?= htmlspecialchars($p['jumlah'] ?? '0'); ?>
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
                                    <?php if ($has_borrowing): ?>
                                        <a href="akses_file.php?type=aktif&id=<?= $p['id_arsip_aktif']; ?>"
                                           target="_blank"
                                           class="btn btn-success btn-sm">
                                            <i class="bi bi-file-earmark-pdf"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="openBorrowModal(
                                                <?= $p['id_arsip_aktif']; ?>,
                                                'aktif',
                                                '<?= addslashes($p['uraian_informasi']); ?>',
                                                '<?= addslashes($p['kode_klasifikasi']); ?>',
                                                '<?= $p['id_kode']; ?>',
                                                '<?= $p['id_sub']; ?>',
                                                '<?= $p['id_subsub']; ?>'
                                            )">
                                            <i class="bi bi-hand-index-thumb"></i> Pinjam
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Tidak ada data arsip aktif
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
