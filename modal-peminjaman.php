<!-- Modal Form Peminjaman Arsip (Bootstrap 5) -->
<div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="borrowModalLabel">
                    <i class="fas fa-file-signature me-2"></i>Form Peminjaman Arsip
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="pinjam-arsip.php" method="POST" id="formPeminjaman">
                    <input type="hidden" name="arsip_type" id="modal_arsip_type">
                    <input type="hidden" name="arsip_id" id="modal_arsip_id">

                    <!-- Baris 1: Tanggal & Periode -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Peminjaman <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pinjam" required class="form-control"
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Periode (Hari) <span class="text-danger">*</span></label>
                            <input type="number" name="periode" value="3" max="3" min="1" required
                                class="form-control bg-light" readonly>
                            <small class="text-muted">Maksimal 3 hari</small>
                        </div>
                    </div>

                    <!-- Uraian Informasi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Uraian Informasi Arsip <span
                                class="text-danger">*</span></label>
                        <textarea name="uraian_informasi" id="modal_uraian" rows="2" required readonly
                            class="form-control bg-light"></textarea>
                    </div>

                    <!-- Baris 2: No Box & Klasifikasi -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">No Box <span class="text-danger">*</span></label>
                            <input type="text" name="no_box" required class="form-control"
                                placeholder="Masukkan nomor box">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kode Klasifikasi <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                                <input type="text" id="display_kode_klasifikasi" class="form-control bg-light" readonly>
                                <input type="hidden" name="kode_klasifikasi" id="hidden_kode_klasifikasi">
                            </div>
                        </div>
                    </div>

                    <!-- Accordion untuk Detail Klasifikasi (Cascading Dropdown) -->
                    <div class="accordion mb-3" id="accordionKlasifikasi">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseKlasifikasi" aria-expanded="false"
                                    aria-controls="collapseKlasifikasi">
                                    <i class="fas fa-sitemap me-2"></i> Detail Klasifikasi (Klik untuk ubah)
                                </button>
                            </h2>
                            <div id="collapseKlasifikasi" class="accordion-collapse collapse"
                                aria-labelledby="headingOne" data-bs-parent="#accordionKlasifikasi">
                                <div class="accordion-body">

                                    <!-- Klasifikasi Utama -->
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Klasifikasi Utama</label>
                                        <select name="id_kode" id="klasifikasi_select"
                                            class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Klasifikasi --</option>
                                            <?php
                                            $query_kode = mysqli_query($db, "SELECT * FROM kode_klasifikasi ORDER BY kode_klasifikasi ASC");
                                            while ($k = mysqli_fetch_array($query_kode)) {
                                                echo "<option value='{$k['id_kode']}'>{$k['kode_klasifikasi']} - {$k['deskripsi']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" id="hidden_id_kode">
                                    </div>

                                    <!-- Sub Klasifikasi -->
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Sub Klasifikasi</label>
                                        <select name="id_sub" id="sub_klasifikasi_select"
                                            class="form-select form-select-sm bg-light" disabled>
                                            <option value="">-- Pilih Sub Klasifikasi --</option>
                                        </select>
                                        <input type="hidden" id="hidden_id_sub">
                                    </div>

                                    <!-- Sub-Sub Klasifikasi -->
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Sub-Sub Klasifikasi</label>
                                        <select name="id_subsub" id="sub_sub_klasifikasi_select"
                                            class="form-select form-select-sm bg-light" disabled>
                                            <option value="">-- Pilih Sub-Sub Klasifikasi --</option>
                                        </select>
                                        <input type="hidden" id="hidden_id_subsub">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 3: Pemilik & Alasan -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pemilik Arsip <span class="text-danger">*</span></label>
                            <input type="text" name="pemilik_arsip" required class="form-control"
                                placeholder="Nama pemilik/pencipta arsip">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Alasan Peminjaman <span
                                    class="text-danger">*</span></label>
                            <textarea name="alasan_pinjam" rows="1" required class="form-control"
                                placeholder="Contoh: Referensi audit"></textarea>
                        </div>
                    </div>

                    <!-- Baris 4: Data Peminjam -->
                    <div class="p-3 bg-light rounded border">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-user me-2"></i>Data Peminjam</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                                <input type="text" name="nama_peminjam" required class="form-control"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Instansi / Unit Kerja <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="instansi_peminjam" required class="form-control"
                                    placeholder="Asal instansi/unit">
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" form="formPeminjaman" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Peminjaman
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variable for modal instance
    let borrowModalInstance = null;

    /**
     * Membuka modal peminjaman dan mengisi data awal
     */
    function openBorrowModal(id, type, uraian, kode, id_kode, id_sub, id_subsub) {
        // Set basic fields
        document.getElementById('modal_arsip_id').value = id;
        document.getElementById('modal_arsip_type').value = type;
        document.getElementById('modal_uraian').value = uraian;
        document.getElementById('display_kode_klasifikasi').value = kode;
        document.getElementById('hidden_kode_klasifikasi').value = kode;

        // Reset inputs
        document.querySelector('input[name="no_box"]').value = '';
        document.querySelector('input[name="pemilik_arsip"]').value = '';
        document.querySelector('textarea[name="alasan_pinjam"]').value = '';
        document.querySelector('input[name="nama_peminjam"]').value = '';
        document.querySelector('input[name="instansi_peminjam"]').value = '';

        // Reset Dropdowns
        document.getElementById('klasifikasi_select').value = id_kode;
        document.getElementById('hidden_id_kode').value = id_kode;

        // Reset Accordion state (close it)
        const collapseEl = document.getElementById('collapseKlasifikasi');
        if (collapseEl.classList.contains('show')) {
            new bootstrap.Collapse(collapseEl, { toggle: false }).hide();
        }

        // Logic untuk mengisi Sub Klasifikasi jika ada
        if (id_sub && id_sub !== 'undefined' && id_sub !== '') {
            fetch(`pinjam-arsip.php?action=get_sub_klasifikasi&id_kode=${id_kode}`)
                .then(response => response.json())
                .then(data => {
                    const subSelect = document.getElementById('sub_klasifikasi_select');
                    subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_sub;
                        option.textContent = item.nama_sub;
                        if (item.id_sub == id_sub) option.selected = true;
                        subSelect.appendChild(option);
                    });
                    subSelect.disabled = false;
                    subSelect.classList.remove('bg-light');
                    document.getElementById('hidden_id_sub').value = id_sub;
                });
        } else {
            document.getElementById('sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
            document.getElementById('sub_klasifikasi_select').disabled = true;
        }

        // Logic untuk mengisi Sub-Sub Klasifikasi jika ada
        if (id_subsub && id_subsub !== 'undefined' && id_subsub !== '') {
            fetch(`pinjam-arsip.php?action=get_sub_sub_klasifikasi&id_sub=${id_sub}`)
                .then(response => response.json())
                .then(data => {
                    const subSubSelect = document.getElementById('sub_sub_klasifikasi_select');
                    subSubSelect.innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id_subsub;
                        option.textContent = item.nama_subsub;
                        if (item.id_subsub == id_subsub) option.selected = true;
                        subSubSelect.appendChild(option);
                    });
                    subSubSelect.disabled = false;
                    subSubSelect.classList.remove('bg-light');
                    document.getElementById('hidden_id_subsub').value = id_subsub;
                });
        } else {
            document.getElementById('sub_sub_klasifikasi_select').innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
            document.getElementById('sub_sub_klasifikasi_select').disabled = true;
        }

        // Show Modal using Bootstrap 5
        if (!borrowModalInstance) {
            borrowModalInstance = new bootstrap.Modal(document.getElementById('borrowModal'));
        }
        borrowModalInstance.show();
    }

    /**
     * Logic Cascading Dropdown (Sama seperti sebelumnya tapi disesuaikan selectornya)
     */

    // Klasifikasi Change
    document.getElementById('klasifikasi_select').addEventListener('change', function () {
        const idKode = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const kodeText = selectedOption.text.split(' - ')[0]; // Ambil kodenya saja

        document.getElementById('hidden_id_kode').value = idKode;

        // Update display
        document.getElementById('display_kode_klasifikasi').value = kodeText;
        document.getElementById('hidden_kode_klasifikasi').value = kodeText;

        // Reset Sub & SubSub
        const subSelect = document.getElementById('sub_klasifikasi_select');
        subSelect.innerHTML = '<option value="">Loading...</option>';
        subSelect.disabled = true;

        const subSubSelect = document.getElementById('sub_sub_klasifikasi_select');
        subSubSelect.innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
        subSubSelect.disabled = true;

        if (idKode) {
            fetch(`pinjam-arsip.php?action=get_sub_klasifikasi&id_kode=${idKode}`)
                .then(res => res.json())
                .then(data => {
                    subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id_sub;
                        opt.textContent = item.nama_sub;
                        subSelect.appendChild(opt);
                    });
                    subSelect.disabled = false;
                    subSelect.classList.remove('bg-light');
                });
        }
    });

    // Sub Klasifikasi Change
    document.getElementById('sub_klasifikasi_select').addEventListener('change', function () {
        const idSub = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const namaSub = selectedOption.text;

        document.getElementById('hidden_id_sub').value = idSub;

        // Update display kode
        let currentCode = document.getElementById('klasifikasi_select').options[document.getElementById('klasifikasi_select').selectedIndex].text.split(' - ')[0];
        let fullCode = `${currentCode} > ${namaSub}`;
        document.getElementById('display_kode_klasifikasi').value = fullCode;
        document.getElementById('hidden_kode_klasifikasi').value = fullCode;

        // Load SubSub
        const subSubSelect = document.getElementById('sub_sub_klasifikasi_select');
        subSubSelect.innerHTML = '<option value="">Loading...</option>';
        subSubSelect.disabled = true;

        if (idSub) {
            fetch(`pinjam-arsip.php?action=get_sub_sub_klasifikasi&id_sub=${idSub}`)
                .then(res => res.json())
                .then(data => {
                    subSubSelect.innerHTML = '<option value="">-- Pilih Sub-Sub Klasifikasi --</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id_subsub;
                        opt.textContent = item.nama_subsub;
                        subSubSelect.appendChild(opt);
                    });
                    subSubSelect.disabled = false;
                    subSubSelect.classList.remove('bg-light');
                });
        }
    });

    // Sub Sub Klasifikasi Change
    document.getElementById('sub_sub_klasifikasi_select').addEventListener('change', function () {
        const idSubSub = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const namaSubSub = selectedOption.text;

        document.getElementById('hidden_id_subsub').value = idSubSub;

        // Update display kode
        let klasifikasiSelect = document.getElementById('klasifikasi_select');
        let currentCode = klasifikasiSelect.options[klasifikasiSelect.selectedIndex].text.split(' - ')[0];

        let subSelect = document.getElementById('sub_klasifikasi_select');
        let namaSub = subSelect.options[subSelect.selectedIndex].text;

        let fullCode = `${currentCode} > ${namaSub} > ${namaSubSub}`;
        document.getElementById('display_kode_klasifikasi').value = fullCode;
        document.getElementById('hidden_kode_klasifikasi').value = fullCode;
    });

</script>