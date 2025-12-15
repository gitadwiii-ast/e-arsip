<?php
/**
 * BACKEND PEMINJAMAN ARSIP
 * File ini menangani proses peminjaman arsip dengan sistem 3 hari
 * 
 * SISTEM PEMINJAMAN 3 HARI:
 * - User dapat meminjam file arsip maksimal 3 hari
 * - Tanggal expired dihitung otomatis: tanggal_pinjam + 3 hari
 * - Setelah expired, file tidak bisa diakses lagi
 * - User harus meminjam ulang jika ingin akses lagi
 * 
 * FLOW:
 * 1. User mengisi form peminjaman (modal_peminjaman.php)
 * 2. Data dikirim ke file ini via POST
 * 3. Validasi user login & data
 * 4. Cek apakah user sudah meminjam file yang sama
 * 5. Hitung tanggal expired (tanggal_pinjam + 3 hari)
 * 6. Simpan ke database tabel peminjaman_arsip
 * 7. Redirect kembali dengan pesan sukses/error
 */

include 'config.php';
session_start();

// STEP 1: Validasi User Login
// Pastikan user sudah login sebelum bisa meminjam
if (!isset($_SESSION['id'])) {
    header("location:index.php");
    exit();
}

// STEP 2: Ambil Data dari Form POST
// Data ini dikirim dari modal_peminjaman.php
$arsip_type = mysqli_real_escape_string($conn, $_POST['arsip_type']);  // vital/permanen/aktif/inaktif
$arsip_id = mysqli_real_escape_string($conn, $_POST['arsip_id']);      // ID arsip yang dipinjam
$user_id = $_SESSION['id'];                                             // ID user yang meminjam

// Data detail peminjaman
$tanggal_pinjam = mysqli_real_escape_string($conn, $_POST['tanggal_pinjam']);
$uraian_informasi = mysqli_real_escape_string($conn, $_POST['uraian_informasi']);
$no_box = mysqli_real_escape_string($conn, $_POST['no_box']);

// Klasifikasi data dari cascading dropdowns
$kode_klasifikasi = mysqli_real_escape_string($conn, $_POST['kode_klasifikasi']); // Kode lengkap untuk display
$id_kode = isset($_POST['id_kode']) ? mysqli_real_escape_string($conn, $_POST['id_kode']) : '';
$id_sub = isset($_POST['id_sub']) ? mysqli_real_escape_string($conn, $_POST['id_sub']) : '';
$id_subsub = isset($_POST['id_subsub']) ? mysqli_real_escape_string($conn, $_POST['id_subsub']) : '';

$pemilik_arsip = mysqli_real_escape_string($conn, $_POST['pemilik_arsip']);
$periode = mysqli_real_escape_string($conn, $_POST['periode']);
$alasan_peminjaman = mysqli_real_escape_string($conn, $_POST['alasan_peminjaman']);
$nama_peminjam = mysqli_real_escape_string($conn, $_POST['nama_peminjam']);
$instansi_peminjam = mysqli_real_escape_string($conn, $_POST['instansi_peminjam']);

// STEP 3: Validasi Tipe Arsip
// Hanya terima tipe arsip yang valid
$valid_types = ['vital', 'permanen', 'aktif', 'inaktif'];
if (!in_array($arsip_type, $valid_types)) {
    header("location:arsip_vital.php?alert=error");
    exit();
}

// STEP 4: Cek Duplikasi Peminjaman
// Cegah user meminjam file yang sama jika masih aktif
// User baru bisa pinjam lagi setelah peminjaman sebelumnya expired
$check_query = "SELECT * FROM peminjaman_arsip 
                WHERE user_id = '$user_id' 
                AND arsip_type = '$arsip_type' 
                AND arsip_id = '$arsip_id' 
                AND status = 'aktif' 
                AND tanggal_expired > NOW()";  // Cek apakah masih belum expired
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // User sudah meminjam file ini dan masih aktif
    header("location:arsip_$arsip_type.php?alert=sudah_dipinjam");
    exit();
}

// STEP 5: HITUNG TANGGAL EXPIRED (SISTEM 3 HARI)
// ================================================
// PENJELASAN SISTEM 3 HARI:
// - Periode maksimal adalah 3 hari (72 jam)
// - Jika user input periode > 3, akan dipaksa jadi 3
// - Jika user input periode < 1, akan dipaksa jadi 1
// - Tanggal expired = tanggal_pinjam + periode hari
// 
// CONTOH:
// Jika tanggal_pinjam = 2025-12-10 09:00:00
// Dan periode = 3 hari
// Maka tanggal_expired = 2025-12-13 09:00:00
// 
// Setelah tanggal 2025-12-13 09:00:00, file tidak bisa diakses lagi
// ================================================
$periode_days = min(3, max(1, intval($periode))); // Pastikan antara 1-3 hari
$tanggal_expired = date('Y-m-d H:i:s', strtotime($tanggal_pinjam . " +$periode_days days"));

// STEP 6: Simpan Data Peminjaman ke Database
// Data disimpan ke tabel peminjaman_arsip dengan status 'aktif'
$insert_query = "INSERT INTO peminjaman_arsip 
                (user_id, arsip_type, arsip_id, tanggal_pinjam, tanggal_expired, status,
                 uraian_informasi, no_box, kode_klasifikasi, pemilik_arsip, periode,
                 alasan_peminjaman, nama_peminjam, instansi_peminjam) 
                VALUES 
                ('$user_id', '$arsip_type', '$arsip_id', '$tanggal_pinjam', '$tanggal_expired', 'aktif',
                 '$uraian_informasi', '$no_box', '$kode_klasifikasi', '$pemilik_arsip', '$periode_days hari',
                 '$alasan_peminjaman', '$nama_peminjam', '$instansi_peminjam')";

// STEP 7: Redirect dengan Pesan
// Kembali ke halaman arsip dengan alert sukses atau error
if (mysqli_query($conn, $insert_query)) {
    // Berhasil menyimpan peminjaman
    header("location:arsip_$arsip_type.php?alert=pinjam_berhasil");
} else {
    // Gagal menyimpan (error database)
    header("location:arsip_$arsip_type.php?alert=error");
}
?>