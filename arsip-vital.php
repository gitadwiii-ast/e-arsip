<?php
$user_id = $_SESSION['id'] ?? null;
?>

<style>
    /* table th,
    table td {
        white-space: normal;
        word-wrap: break-word;
        vertical-align: middle;
    }
    table {
    width: 100%;
    table-layout: fixed;
    } */
</style>

<div class="d-flex">
    <?php include 'header.php'; ?>

    <main class="flex-grow-1 p-4"
          style="background-color:#f3f4f6; min-height:100vh; min-width:0;">
        <div class="container-fluid">

            <div class="card shadow-lg rounded-3">

                <!-- HEADER -->
                <div class="card-header text-white"
                     style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Vital
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
                                    <th>No</th>
                                    <th>Uraian Informasi</th>
                                    <th>Asal</th>
                                    <th>Klasifikasi</th>
                                    <th>Jenis Arsip</th>
                                    <th>Nomor Arsip</th>
                                    <th>Retensi</th>
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <?php $dipinjam = !empty($row['id_peminjaman']); ?>

                                        <tr>
                                            <td><?= $no++; ?></td>

                                            <td>
                                                <?= htmlspecialchars($row['uraian_informasi']); ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <?= ucfirst($row['asal_arsip']); ?>
                                                </span>
                                            </td>

                                            <td>
                                                <strong><?= $row['kode_klasifikasi']; ?></strong><br>
                                                <small class="text-muted">
                                                    <?= $row['deskripsi_kode']; ?>
                                                </small>
                                            </td>

                                            <td>
                                                    <?= htmlspecialchars($row['jenis_arsip']); ?>
                                            </td>

                                            
                                            <td class="font-monospace">
                                                <?= $row['nomor_arsip']; ?>
                                            </td>

                                            <td>
                                                <?= $row['retensi']; ?> Tahun
                                            </td>

                                            <td>
                                                <?= $row['lokasi_simpan']; ?>
                                            </td>

                                            <td class="text-center">
                                                <?php if ($dipinjam): ?>
                                                    <a href="akses_file.php?type=vital&id=<?= $row['id_arsip_vital']; ?>"
                                                       target="_blank"
                                                       class="btn btn-success btn-sm">
                                                        <i class="bi bi-file-earmark-pdf"></i> Lihat
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button"
                                                            onclick="openBorrowModal(<?= $row['id_arsip_vital']; ?>,'vital')"
                                                            class="btn btn-primary btn-sm">
                                                        <i class="bi bi-hand-index-thumb"></i> Pinjam
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            Tidak ada data arsip vital
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

