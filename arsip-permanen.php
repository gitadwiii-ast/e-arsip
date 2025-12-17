<?php
session_start();
include 'konektor.php';
$user_id = $_SESSION['id'] ?? null;
include 'header.php';
?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main class="flex-grow-1 p-4" style="background-color:#f3f4f6; min-height:100vh; min-width: 0;">
        <div class="container-fluid">
            <div class="card shadow-lg rounded-3">


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
                    $where = "";
                    if (!empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($db, $_GET['search']);
                        $where = "WHERE av.uraian_informasi LIKE '%$search%'
                          OR av.nomor_arsip LIKE '%$search%'
                          OR av.lokasi_simpan LIKE '%$search%'";
                    }
                    ?>

                    <!-- TABLE -->

                    <div class="table-responsive table-scroll">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>No</th>
                                    <th>Uraian Informasi</th>
                                    <th>Klasifikasi</th>
                                    <th>Jenis Arsip</th>
                                    <th>Tingkat Perkembangan</th>
                                    <th>Kurun Waktu</th>
                                    <th>Jumlah</th>
                                    <th>Ket. Nasib Akhir / No Box</th>
                                    <th>Aksi</th>
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
                                // $user_id = $_SESSION['id'];
                                $query = "
                                 SELECT ap.*, 
                                    kk.kode_klasifikasi,
                                    sk.nama_sub,
                                    ssk.nama_subsub,
                                    tp.nama_tingkat,
                                    na.nama_nasib,
                                    ja.nama_jenis,
                                    p.id_peminjaman
                                FROM arsip_permanen ap
                                LEFT JOIN kode_klasifikasi kk ON ap.id_kode = kk.id_kode
                                LEFT JOIN sub_klasifikasi sk ON ap.id_sub = sk.id_sub
                                LEFT JOIN sub_sub_klasifikasi ssk ON ap.id_subsub = ssk.id_subsub
                                LEFT JOIN tingkat_perkembangan tp ON ap.id_tingkat = tp.id_tingkat
                                LEFT JOIN nasib_akhir na ON ap.id_nasib = na.id_nasib
                                LEFT JOIN jenis_arsip ja ON ap.id_jenis = ja.id_jenis
                                LEFT JOIN peminjaman_arsip p
                                    ON p.arsip_type = 'permanen'
                                    AND p.arsip_id = ap.id_arsip_permanen
                                    AND p.user_id = '$user_id'
                                    AND p.status = 'aktif'
                                    AND p.tanggal_expired > NOW()
                                $where
                                ORDER BY ap.id_arsip_permanen DESC
                            ";

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
                                                    <button class="btn btn-primary btn-sm" onclick="openBorrowModal(
                                                        '<?= $p['id_arsip_permanen']; ?>',
                                                        'permanen',
                                                        '<?= addslashes($p['uraian_informasi']); ?>',
                                                        '<?= addslashes($p['kode_klasifikasi']); ?>',
                                                        '<?= $p['id_kode']; ?>',
                                                        '<?= $p['id_sub'] ?? ''; ?>',
                                                        '<?= $p['id_subsub'] ?? ''; ?>'
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