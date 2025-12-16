<?php
$user_id = $_SESSION['id'] ?? null;
?>


<div class="d-flex">
    <?php include 'header.php';?>
    <main class="flex-grow-1 p-4" style="background-color:#f3f4f6; min-height:100vh; min-width: 0;">
        <div class="container-fluid">
            <div class="card shadow-lg rounded-3 overflow-hidden">


        <!-- HEADER -->
        <div class="card-header text-white d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Vital
                    </h5>
        </div>

        <!-- BODY -->
        <div class="card-body">
            <form method="GET" class="row g-2 mb-4 align-items-center">
                <div class="col-md-10">
                        <input type="text" name="search"
                        class="form-control"
                        placeholder="Cari berdasarkan uraian, nomor arsip, atau lokasi..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari</button>
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
            $query = "SELECT av.*,
                             kk.kode_klasifikasi,
                             kk.deskripsi AS deskripsi_kode,
                             p.id_peminjaman,
                             p.tanggal_expired
                      FROM arsip_vital av
                      LEFT JOIN kode_klasifikasi kk 
                        ON av.id_kode = kk.id_kode
                      LEFT JOIN peminjaman_arsip p 
                        ON p.arsip_type = 'vital'
                        AND p.arsip_id = av.id_arsip_vital
                        AND p.user_id = '$user_id'
                        AND p.status = 'aktif'
                        AND p.tanggal_expired > NOW()
                      $where
                      ORDER BY av.id_arsip_vital DESC";

            $result = mysqli_query($db, $query);
            $no = 1;
            ?>

            <!-- TABLE -->

            <div class="table-responsive table-scroll">
                <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>No</th>                                
                                    <th style="min-width: 150px;">Uraian Informasi</th>
                                    <th style="min-width: 100px;">Asal</th>
                                    <th style="min-width: 150px;">Klasifikasi</th>
                                    <th style="min-width: 130px;">Nomor Arsip</th>
                                    <th style="min-width: 100px;">Retensi</th>
                                    <th style="min-width: 150px;">Lokasi</th>
                                    <th style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $dipinjam = !empty($row['id_peminjaman']);
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($row['uraian_informasi']); ?>
                            </td>

                            <td>
                                <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                    <?= ucfirst($row['asal_arsip']); ?>
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-primary">
                                    <?= $row['kode_klasifikasi']; ?>
                                </div>
                                <small class="text-muted">
                                    <?= $row['deskripsi_kode']; ?>
                                </small>
                            </td>

                            <td class="font-monospace">
                                <?= $row['nomor_arsip']; ?>
                            </td>

                            <td>
                                <?= $row['retensi']; ?> Tahun
                            </td>

                            <td>
                                <i class="bi bi-geo-alt text-secondary"></i>
                                <?= $row['lokasi_simpan']; ?>
                            </td>

                            <td class="text-center">
                                <?php if ($dipinjam) { ?>
                                    <a href="akses_file.php?type=vital&id=<?= $row['id_arsip_vital']; ?>"
                                       target="_blank"
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat
                                    </a>
                                <?php } else { ?>
                                    <button
                                        onclick="openBorrowModal(<?= $row['id_arsip_vital']; ?>,'vital')"
                                        class="btn btn-primary btn-sm px-4">
                                        <i class="bi bi-hand-index-thumb"></i> Pinjam
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Tidak ada data arsip vital
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>                        
                </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
