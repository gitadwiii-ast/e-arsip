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
                        <i class="fas fa-archive me-2"></i> Data Arsip Permanen
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
            } ?>

            <!-- TABLE -->

            <div class="table-responsive table-scroll">
                <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>No</th>                                
                                    <th style="min-width: 150px;">Uraian Informasi</th>
                                    <th style="min-width: 100px;">No Arsip/Berkas</th>
                                    <th style="min-width: 150px;">Klasifikasi</th>
                                    <th style="min-width: 130px;">Jenis/Series Arsip</th>
                                    <th style="min-width: 100px;">Kurun Waktu</th>
                                    <th style="min-width: 100px;">Retensi Arsip</th>
                                    <th style="min-width: 150px;">Jumlah</th>
                                    <th style="min-width: 150px;">Tingkat Perkembangan</th>
                                    <th style="min-width: 150px;">Keterangan</th>
                                    <th style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                            $no = 1;
                            $where_conditions = [];

                            // Filter by search
                            if (isset($_GET['search']) && $_GET['search'] != '') {
                                $search = mysqli_real_escape_string($db, $_GET['search']);
                                $where_conditions[] = "(ap.uraian_informasi LIKE '%$search%' OR ap.nomor_arsip LIKE '%$search%' OR ap.lokasi_simpan LIKE '%$search%')";
                            }

                            $where = "";
                            if (count($where_conditions) > 0) {
                                $where = "WHERE " . implode(" AND ", $where_conditions);
                            }

                            // Query for arsip_permanen with JOIN to kode_klasifikasi and peminjaman_arsip
                            $user_id = $_SESSION['id'];
                            $query = "SELECT ai.*, 
                                             ai.id_kode,
                                             ai.id_sub,
                                             ai.id_subsub,
                                             ai.kurun_waktu,
                                             ai.jumlah,
                                             ai.keterangan,
                                             kk.kode_klasifikasi, 
                                             kk.deskripsi as deskripsi_kode,
                                             ja.nama_jenis,
                                             tp.nama_tingkat,
                                             u.nama_lengkap,
                                             p.id_peminjaman,
                                             p.tanggal_expired,
                                             p.status as status_pinjam
                                      FROM arsip_inaktif ai
                                      LEFT JOIN kode_klasifikasi kk ON ai.id_kode = kk.id_kode
                                      LEFT JOIN jenis_arsip ja ON ai.id_jenis = ja.id_jenis
                                      LEFT JOIN tingkat_perkembangan tp ON ai.id_tingkat = tp.id_tingkat
                                      LEFT JOIN users u ON ai.user_id = u.id
                                      LEFT JOIN peminjaman_arsip p ON p.arsip_type = 'inaktif' 
                                            AND p.arsip_id = ai.id_arsip_inaktif 
                                            AND p.user_id = '$user_id'
                                            AND p.status = 'aktif'
                                            AND p.tanggal_expired > NOW()
                                      $where
                                      ORDER BY ai.id_arsip_inaktif DESC";

                            $arsip = mysqli_query($db, $query);

                            if (mysqli_num_rows($arsip) > 0) {
                                while ($p = mysqli_fetch_array($arsip)) {
                                    // Check if user has active borrowing
                                    $has_borrowing = !empty($p['id_peminjaman']);
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>

                                        <td><?= htmlspecialchars($p['uraian_informasi']); ?></td>

                                        <!-- KLASIFIKASI -->
                                       <td>
                                            <strong><?= htmlspecialchars($p['kode_klasifikasi'] ?? '-'); ?></strong><br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($p['nama_sub'] ?? '-'); ?> /
                                                <?= htmlspecialchars($p['nama_subsub'] ?? '-'); ?>
                                            </small>
                                        </td>

                                        <!-- JENIS ARSIP -->
                                        <td>
                                            <?= htmlspecialchars($p['nama_jenis'] ?? 'Tidak diketahui'); ?>
                                        </td>

                                        <!-- TINGKAT PERKEMBANGAN -->
                                        <td>
                                            <?= htmlspecialchars($p['nama_tingkat'] ?? '-'); ?>
                                        </td>

                                        <!-- KURUN WAKTU -->
                                        <td>
                                            <?= htmlspecialchars($p['kurun_waktu'] ?? '-'); ?>
                                        </td>

                                        <!-- JUMLAH -->
                                        <td class="text-center">
                                            <?= htmlspecialchars($p['jumlah'] ?? '-'); ?>
                                        </td>

                                        <!-- NASIB AKHIR -->
                                        <td>
                                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                                <?= htmlspecialchars($p['nama_nasib'] ?? 'Belum ditentukan'); ?>
                                            </span>
                                        </td>

                                        <!-- AKSI -->
                                        <td class="text-center">
                                            <?php if (!empty($p['id_peminjaman'])) { ?>
                                                <a href="akses_file.php?type=permanen&id=<?= $p['id_arsip_permanen']; ?>"
                                                class="btn btn-success btn-sm" target="_blank">
                                                Lihat
                                                </a>
                                            <?php } else { ?>
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="openBorrowModal(
                                                        <?= $p['id_arsip_permanen']; ?>,
                                                        'permanen',
                                                        '<?= addslashes($p['uraian_informasi']); ?>',
                                                        '<?= addslashes($p['kode_klasifikasi']); ?>'
                                                    )">
                                                    Pinjam
                                                </button>
                                            <?php } ?>
                                        </td>
                                    </tr>

                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="8"
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
