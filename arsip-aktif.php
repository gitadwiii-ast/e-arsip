<?php include 'header.php'; ?>

<div class="container-fluid mt-4">

    <!-- CARD -->
    <div class="card shadow-sm border-0">

        <!-- HEADER -->
        <div class="card-header text-white d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(90deg,#4f46e5,#4338ca);">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-archive me-2"></i> Data Arsip Aktif
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
            $user_id = $_SESSION['id'] ?? null;
                            $query = "SELECT aa.*, 
                                             aa.id_kode,
                                             aa.id_sub,
                                             aa.id_subsub,
                                             aa.uraian_informasi_berkas,
                                             aa.no_berkas,
                                             aa.jumlah,
                                             aa.kurun_waktu,
                                             kk.kode_klasifikasi, 
                                             kk.deskripsi as deskripsi_kode,
                                             kkm.nama_keamanan,
                                             u.nama_lengkap,
                                             p.id_peminjaman,
                                             p.tanggal_expired,
                                             p.status as status_pinjam
                                      FROM arsip_aktif aa
                                      LEFT JOIN kode_klasifikasi kk ON aa.id_kode = kk.id_kode
                                      LEFT JOIN klasifikasi_keamanan kkm ON aa.id_keamanan = kkm.id_keamanan
                                      LEFT JOIN users u ON aa.user_id = u.id
                                      LEFT JOIN peminjaman_arsip p ON p.arsip_type = 'aktif' 
                                            AND p.arsip_id = aa.id_arsip_aktif 
                                            AND p.user_id = '$user_id'
                                            AND p.status = 'aktif'
                                            AND p.tanggal_expired > NOW()
                                      $where
                                      ORDER BY aa.id_arsip_aktif DESC";

                            $arsip = mysqli_query($db, $query);
                            $no=1;
            ?>

            <!-- TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>No</th>                                
                                    <th style="min-width: 150px;">Uraian Informasi</th>
                                    <th style="min-width: 150px;">Uraian Info Berkas</th>
                                    <th style="min-width: 150px;">Klasifikasi</th>
                                    <th style="min-width: 130px;">Nomor Berkas</th>
                                    <th style="min-width: 130px;">Jumlah</th>
                                    <th style="min-width: 100px;">Kurun Waktu</th>
                                    <th style="min-width: 100px;">Keamanan</th>
                                    <th style="min-width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php if ($arsip && mysqli_num_rows($arsip) > 0) {
                                while ($p = mysqli_fetch_array($arsip)) {
                                    // Check if user has active borrowing
                                    $has_borrowing = !empty($p['id_peminjaman']);
                                    ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <?php echo $no++; ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <div class="text-gray-900 font-medium">
                                                <?php echo htmlspecialchars($p['uraian_informasi']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <div class="font-mono font-semibold text-green-600">
                                                <?php echo htmlspecialchars($p['kode_klasifikasi']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo htmlspecialchars($p['deskripsi_kode']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($p['uraian_informasi_berkas'] ?? '-'); ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm font-mono">
                                            <?php echo htmlspecialchars($p['no_berkas'] ?? '-'); ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($p['jumlah'] ?? '0'); ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <?php echo htmlspecialchars($p['kurun_waktu'] ?? '-'); ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <span
                                                class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                                <?php echo htmlspecialchars($p['nama_keamanan'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-center">
                                            <div class="flex flex-col gap-2">
                                                <?php if ($has_borrowing) { ?>
                                                    <!-- User has borrowed this file -->
                                                    <a href="akses_file.php?type=aktif&id=<?php echo $p['id_arsip_aktif']; ?>"
                                                        target="_blank"
                                                        class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-sm hover:shadow-md"
                                                        title="Lihat File PDF">
                                                        <i class="fas fa-file-pdf mr-2"></i> Lihat File
                                                    </a>
                                                    <span class="text-xs text-gray-500">
                                                        Berlaku s/d:
                                                        <?php echo date('d/m/Y H:i', strtotime($p['tanggal_expired'])); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <!-- User hasn't borrowed this file -->
                                                    <button type="button"
                                                        onclick="openBorrowModal(<?php echo $p['id_arsip_aktif']; ?>, 'aktif', '<?php echo addslashes($p['uraian_informasi']); ?>', '<?php echo addslashes($p['kode_klasifikasi']); ?>', '<?php echo $p['id_kode']; ?>', '<?php echo $p['id_sub']; ?>', '<?php echo $p['id_subsub']; ?>')"
                                                        class="btn btn-primary btn-sm px-4">
                                                        <i class="bi bi-hand-index-thumb"></i> Pinjam
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="9"
                                        class="px-4 py-8 border-b border-gray-200 bg-white text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-lg">Tidak ada data arsip aktif</p>
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
</div>

<script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>


<?php include 'footer.php'; ?>