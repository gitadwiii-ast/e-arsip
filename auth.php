<?php
session_start();
include 'konektor.php'; // file koneksi ke database

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($konektor, $_POST['username']);
    $password = mysqli_real_escape_string($konektor, $_POST['password']);

    // Cek user
    $query = mysqli_query($konektor, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {

        // Cek password (ubah sesuai kebutuhan, plain / md5 / password_hash)
        if (password_verify($password, $data['password'])) {

            // Simpan session user
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['level'] = $data['level'];

            header("location: dashboard.php");
            exit();

        } else {
            header("location: index.php?alert=gagal");
            exit();
        }

    } else {
        header("location: index.php?alert=gagal");
        exit();
    }
}
?>
