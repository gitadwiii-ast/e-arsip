<!-- Modal Form Peminjaman Arsip -->
<div id="borrowModal"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-[9999]">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white my-10">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-900">Form Peminjaman Arsip</h3>
            <button onclick="closeBorrowModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form action="pinjam_arsip.php" method="POST" class="mt-4">
            <input type="hidden" name="arsip_type" id="modal_arsip_type">
            <input type="hidden" name="arsip_id" id="modal_arsip_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal Peminjaman -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Peminjaman *</label>
                    <input type="date" name="tanggal_pinjam" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                        value="<?php echo date('Y-m-d'); ?>">
                </div>

                <!-- Periode -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Periode (Hari) *</label>
                    <input type="number" name="periode" value="3" max="3" min="1" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                        readonly>
                    <p class="text-xs text-gray-500 mt-1">Maksimal 3 hari</p>
                </div>
            </div>

            <!-- Uraian Informasi -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Uraian Informasi Arsip *</label>
                <textarea name="uraian_informasi" id="modal_uraian" rows="2" required readonly
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <!-- No Box -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">No Box *</label>
                    <input type="text" name="no_box" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                        placeholder="Masukkan nomor box">
                </div>

                <!-- Klasifikasi Dropdown -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Klasifikasi *</label>
                    <select id="klasifikasi_select" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500">
                        <option value="">-- Pilih Klasifikasi --</option>
                        <?php
                        include 'config.php';
                        $kode_query = mysqli_query($conn, "SELECT id_kode, kode_klasifikasi, deskripsi FROM kode_klasifikasi ORDER BY kode_klasifikasi ASC");
                        while ($kode = mysqli_fetch_array($kode_query)) {
                            echo "<option value='{$kode['id_kode']}'>{$kode['kode_klasifikasi']} - {$kode['deskripsi']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <!-- Sub Klasifikasi Dropdown -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sub Klasifikasi *</label>
                    <select id="sub_klasifikasi_select" required disabled
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500">
                        <option value="">-- Pilih Sub Klasifikasi --</option>
                    </select>
                </div>

                <!-- Sub-Sub Klasifikasi Dropdown -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sub-Sub Klasifikasi</label>
                    <select id="sub_sub_klasifikasi_select" disabled
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500">
                        <option value="">-- Pilih Sub-Sub Klasifikasi --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Opsional</p>
                </div>
            </div>

            <!-- Hidden fields untuk menyimpan nilai yang akan dikirim -->
            <input type="hidden" name="id_kode" id="hidden_id_kode">
            <input type="hidden" name="id_sub" id="hidden_id_sub">
            <input type="hidden" name="id_subsub" id="hidden_id_subsub">
            <input type="hidden" name="kode_klasifikasi" id="hidden_kode_klasifikasi">

            <!-- Display Kode Klasifikasi Lengkap -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kode Klasifikasi Lengkap</label>
                <input type="text" id="display_kode_klasifikasi" readonly
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none"
                    placeholder="Akan terisi otomatis setelah memilih klasifikasi">
            </div>

            <!-- Pemilik Arsip -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Pemilik Arsip *</label>
                <input type="text" name="pemilik_arsip" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                    placeholder="Masukkan pemilik arsip">
            </div>

            <!-- Alasan Peminjaman -->
            <div class="mt-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Alasan Peminjaman *</label>
                <textarea name="alasan_peminjaman" rows="2" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                    placeholder="Masukkan alasan peminjaman"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <!-- Nama Peminjam -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Peminjam *</label>
                    <input type="text" name="nama_peminjam" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                        placeholder="Nama lengkap peminjam">
                </div>

                <!-- Instansi Peminjam -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Instansi Peminjam *</label>
                    <input type="text" name="instansi_peminjam" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                        placeholder="Nama instansi">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-4 border-t">
                <button type="button" onclick="closeBorrowModal()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Batal
                </button>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-save mr-2"></i> Pinjam Arsip
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function openBorrowModal(arsipId, arsipType, uraian, kode, idKode = '', idSub = '', idSubsub = '') {
        document.getElementById('modal_arsip_id').value = arsipId;
        document.getElementById('modal_arsip_type').value = arsipType;
        document.getElementById('modal_uraian').value = uraian;

        // Reset all dropdowns first
        document.getElementById('klasifikasi_select').value = '';
        document.getElementById('sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
        document.getElementById('sub_klasifikasi_select').disabled = true;
        document.getElementById('sub_sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
        document.getElementById('sub_sub_klasifikasi_select').disabled = true;
        document.getElementById('display_kode_klasifikasi').value = '';

        // Auto-fill klasifikasi if provided
        if (idKode) {
            // Set klasifikasi dropdown
            document.getElementById('klasifikasi_select').value = idKode;
            document.getElementById('hidden_id_kode').value = idKode;

            // Get selected klasifikasi text
            const klasifikasiSelect = document.getElementById('klasifikasi_select');
            const selectedKlasifikasi = klasifikasiSelect.options[klasifikasiSelect.selectedIndex].text.split(' - ')[0];
            document.getElementById('display_kode_klasifikasi').value = selectedKlasifikasi;
            document.getElementById('hidden_kode_klasifikasi').value = selectedKlasifikasi;

            // Load sub klasifikasi
            try {
                const response = await fetch(`get_sub_klasifikasi.php?id_kode=${idKode}`);
                const data = await response.json();

                const subSelect = document.getElementById('sub_klasifikasi_select');
                subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';

                if (data.length > 0) {
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_sub;
                        option.textContent = item.nama_sub;
                        subSelect.appendChild(option);
                    });
                    subSelect.disabled = false;
                    subSelect.classList.remove('bg-gray-100');

                    // Auto-select sub klasifikasi if provided
                    if (idSub) {
                        subSelect.value = idSub;
                        document.getElementById('hidden_id_sub').value = idSub;

                        // Update display
                        const namaSub = subSelect.options[subSelect.selectedIndex].textContent;
                        const fullCode = `${selectedKlasifikasi} > ${namaSub}`;
                        document.getElementById('display_kode_klasifikasi').value = fullCode;
                        document.getElementById('hidden_kode_klasifikasi').value = fullCode;

                        // Load sub-sub klasifikasi
                        const response2 = await fetch(`get_sub_sub_klasifikasi.php?id_sub=${idSub}`);
                        const data2 = await response2.json();

                        const subSubSelect = document.getElementById('sub_sub_klasifikasi_select');
                        subSubSelect.innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';

                        if (data2.length > 0) {
                            data2.forEach(item => {
                                const option = document.createElement('option');
                                option.value = item.id_subsub;
                                option.textContent = item.nama_subsub;
                                subSubSelect.appendChild(option);
                            });
                            subSubSelect.disabled = false;
                            subSubSelect.classList.remove('bg-gray-100');

                            // Auto-select sub-sub klasifikasi if provided
                            if (idSubsub) {
                                subSubSelect.value = idSubsub;
                                document.getElementById('hidden_id_subsub').value = idSubsub;

                                // Update display
                                const namaSubSub = subSubSelect.options[subSubSelect.selectedIndex].textContent;
                                const fullCode2 = `${selectedKlasifikasi} > ${namaSub} > ${namaSubSub}`;
                                document.getElementById('display_kode_klasifikasi').value = fullCode2;
                                document.getElementById('hidden_kode_klasifikasi').value = fullCode2;
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading klasifikasi data:', error);
            }
        }

        document.getElementById('borrowModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeBorrowModal() {
        document.getElementById('borrowModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Event listener untuk Klasifikasi dropdown
    document.getElementById('klasifikasi_select')?.addEventListener('change', function () {
        const idKode = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const kodeKlasifikasi = selectedOption.text.split(' - ')[0];

        // Update hidden field
        document.getElementById('hidden_id_kode').value = idKode;

        // Reset sub klasifikasi dan sub-sub klasifikasi
        document.getElementById('sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
        document.getElementById('sub_klasifikasi_select').disabled = true;
        document.getElementById('sub_sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
        document.getElementById('sub_sub_klasifikasi_select').disabled = true;
        document.getElementById('hidden_id_sub').value = '';
        document.getElementById('hidden_id_subsub').value = '';

        if (idKode) {
            // Update display
            document.getElementById('display_kode_klasifikasi').value = kodeKlasifikasi;
            document.getElementById('hidden_kode_klasifikasi').value = kodeKlasifikasi;

            // Load sub klasifikasi via AJAX
            fetch(`get_sub_klasifikasi.php?id_kode=${idKode}`)
                .then(response => response.json())
                .then(data => {
                    const subSelect = document.getElementById('sub_klasifikasi_select');
                    subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';

                    if (data.length > 0) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_sub;
                            option.textContent = item.nama_sub;
                            option.dataset.idSub = item.id_sub;
                            subSelect.appendChild(option);
                        });
                        subSelect.disabled = false;
                        subSelect.classList.remove('bg-gray-100');
                    } else {
                        // Jika tidak ada sub klasifikasi, enable form submission
                        subSelect.disabled = true;
                        subSelect.required = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading sub klasifikasi:', error);
                    alert('Gagal memuat sub klasifikasi');
                });
        } else {
            document.getElementById('display_kode_klasifikasi').value = '';
            document.getElementById('hidden_kode_klasifikasi').value = '';
        }
    });

    // Event listener untuk Sub Klasifikasi dropdown
    document.getElementById('sub_klasifikasi_select')?.addEventListener('change', function () {
        const idSub = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const namaSub = selectedOption.textContent;

        // Update hidden field
        document.getElementById('hidden_id_sub').value = idSub;

        // Reset sub-sub klasifikasi
        document.getElementById('sub_sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
        document.getElementById('sub_sub_klasifikasi_select').disabled = true;
        document.getElementById('hidden_id_subsub').value = '';

        if (idSub) {
            // Update display kode klasifikasi
            const kodeKlasifikasi = document.getElementById('display_kode_klasifikasi').value;
            const fullCode = `${kodeKlasifikasi} > ${namaSub}`;
            document.getElementById('display_kode_klasifikasi').value = fullCode;
            document.getElementById('hidden_kode_klasifikasi').value = fullCode;

            // Load sub-sub klasifikasi via AJAX
            fetch(`get_sub_sub_klasifikasi.php?id_sub=${idSub}`)
                .then(response => response.json())
                .then(data => {
                    const subSubSelect = document.getElementById('sub_sub_klasifikasi_select');
                    subSubSelect.innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';

                    if (data.length > 0) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_subsub;
                            option.textContent = item.nama_subsub;
                            subSubSelect.appendChild(option);
                        });
                        subSubSelect.disabled = false;
                        subSubSelect.classList.remove('bg-gray-100');
                    }
                })
                .catch(error => {
                    console.error('Error loading sub-sub klasifikasi:', error);
                    // Sub-sub klasifikasi is optional, so don't show error
                });
        } else {
            // Reset to only klasifikasi
            const kodeKlasifikasi = document.getElementById('hidden_id_kode').value;
            const klasifikasiSelect = document.getElementById('klasifikasi_select');
            const selectedKlasifikasi = klasifikasiSelect.options[klasifikasiSelect.selectedIndex].text.split(' - ')[0];
            document.getElementById('display_kode_klasifikasi').value = selectedKlasifikasi;
            document.getElementById('hidden_kode_klasifikasi').value = selectedKlasifikasi;
        }
    });

    // Event listener untuk Sub-Sub Klasifikasi dropdown
    document.getElementById('sub_sub_klasifikasi_select')?.addEventListener('change', function () {
        const idSubSub = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const namaSubSub = selectedOption.textContent;

        // Update hidden field
        document.getElementById('hidden_id_subsub').value = idSubSub;

        if (idSubSub) {
            // Update display kode klasifikasi
            const kodeKlasifikasi = document.getElementById('hidden_id_kode').value;
            const klasifikasiSelect = document.getElementById('klasifikasi_select');
            const selectedKlasifikasi = klasifikasiSelect.options[klasifikasiSelect.selectedIndex].text.split(' - ')[0];

            const subSelect = document.getElementById('sub_klasifikasi_select');
            const namaSub = subSelect.options[subSelect.selectedIndex].textContent;

            const fullCode = `${selectedKlasifikasi} > ${namaSub} > ${namaSubSub}`;
            document.getElementById('display_kode_klasifikasi').value = fullCode;
            document.getElementById('hidden_kode_klasifikasi').value = fullCode;
        } else {
            // Reset to klasifikasi + sub klasifikasi
            const kodeKlasifikasi = document.getElementById('hidden_id_kode').value;
            const klasifikasiSelect = document.getElementById('klasifikasi_select');
            const selectedKlasifikasi = klasifikasiSelect.options[klasifikasiSelect.selectedIndex].text.split(' - ')[0];

            const subSelect = document.getElementById('sub_klasifikasi_select');
            const namaSub = subSelect.options[subSelect.selectedIndex].textContent;

            const fullCode = `${selectedKlasifikasi} > ${namaSub}`;
            document.getElementById('display_kode_klasifikasi').value = fullCode;
            document.getElementById('hidden_kode_klasifikasi').value = fullCode;
        }
    });

    // Close modal when clicking outside
    document.getElementById('borrowModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeBorrowModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !document.getElementById('borrowModal').classList.contains('hidden')) {
            closeBorrowModal();
        }
    });
</script>
