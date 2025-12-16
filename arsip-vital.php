<?php include 'header.php'; ?>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden glass-panel">
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-indigo-700">
                <h3 class="text-lg font-bold text-white flex items-center">
                    Arsip Vital
                </h3>
            </div>

            <div class="p-6">
                <!-- Search Form -->
                <div class="mb-6">
                    <form method="GET" action="arsip_vital.php" class="flex gap-2">
                        <div class="flex-1">
                            <input type="text" name="search"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 transition"
                                placeholder="Cari berdasarkan uraian, nomor arsip, atau lokasi..."
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            <i class="fas fa-search mr-2"></i>Cari
                        </button>
                        <?php if (isset($_GET['search']) && $_GET['search'] != '') { ?>
                            <a href="arsip_vital.php"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                                <i class="fas fa-times mr-2"></i>Reset
                            </a>
                        <?php } ?>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Uraian Informasi</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Asal</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kode Klasifikasi</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nomor Arsip</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Retensi</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Lokasi Simpan</th>
                                <th
                                    class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $where_conditions = [];
                            

                            // Filter by search
                            if (isset($_GET['search']) && $_GET['search'] != '') {
                                $search = mysqli_real_escape_string($db, $_GET['search']);
                                $where_conditions[] = "(av.uraian_informasi LIKE '%$search%' OR av.nomor_arsip LIKE '%$search%' OR av.lokasi_simpan LIKE '%$search%')";
                            }

                            $where = "";
                            if (count($where_conditions) > 0) {
                                $where = "WHERE " . implode(" AND ", $where_conditions);
                            }

                            // Query for arsip_vital with JOIN to kode_klasifikasi and peminjaman_arsip
                            $user_id = $_SESSION['id']?? null;
                            $query = "SELECT av.*, 
                                             av.id_kode,
                                             av.id_sub,
                                             av.id_subsub,
                                             kk.kode_klasifikasi, 
                                             kk.deskripsi as deskripsi_kode,
                                             u.nama_lengkap,
                                             p.id_peminjaman,
                                             p.tanggal_expired,
                                             p.status as status_pinjam
                                      FROM arsip_vital av
                                      LEFT JOIN kode_klasifikasi kk ON av.id_kode = kk.id_kode
                                      LEFT JOIN users u ON av.user_id = u.id
                                      LEFT JOIN peminjaman_arsip p ON p.arsip_type = 'vital' 
                                            AND p.arsip_id = av.id_arsip_vital 
                                            AND p.user_id = '$user_id'
                                            AND p.status = 'aktif'
                                            AND p.tanggal_expired > NOW()
                                      $where
                                      ORDER BY av.id_arsip_vital DESC";

                            $arsip = mysqli_query($db, $query);

                            if (mysqli_num_rows($arsip) > 0) {
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
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $p['asal_arsip'] == 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo ucfirst($p['asal_arsip']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <div class="font-mono font-semibold text-indigo-600">
                                                <?php echo htmlspecialchars($p['kode_klasifikasi']); ?>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo htmlspecialchars($p['deskripsi_kode']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm font-mono">
                                            <?php echo htmlspecialchars($p['nomor_arsip']); ?>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <span class="text-gray-900"><?php echo $p['retensi']; ?> Tahun</span>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                                <?php echo htmlspecialchars($p['lokasi_simpan']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-center">
                                            <div class="flex flex-col gap-2">
                                                <?php if ($has_borrowing) { ?>
                                                    <!-- User has borrowed this file -->
                                                    <a href="akses_file.php?type=vital&id=<?php echo $p['id_arsip_vital']; ?>"
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
                                                        onclick="openBorrowModal(<?php echo $p['id_arsip_vital']; ?>, 'vital', '<?php echo addslashes($p['uraian_informasi']); ?>', '<?php echo addslashes($p['kode_klasifikasi']); ?>', '<?php echo $p['id_kode']; ?>', '<?php echo $p['id_sub']; ?>', '<?php echo $p['id_subsub']; ?>')"
                                                        class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-sm hover:shadow-md"
                                                        title="Pinjam File">
                                                        <i class="fas fa-hand-holding mr-2"></i> Pinjam
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
                                    <td colspan="8"
                                        class="px-4 py-8 border-b border-gray-200 bg-white text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                        <p class="text-lg">Tidak ada data arsip vital</p>
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
</div>

<?php include 'modal-peminjaman.php'; ?>
<?php include 'footer.php'; ?>