<?php
session_start();

// CAPTCHA hanya dibuat sekali
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = rand(10000,99999);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-Arsip</title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="gradient-bg d-flex align-items-center justify-content-center vh-100">

<div class="container px-3">
    <div class="glass-panel shadow-lg rounded-4 p-4 p-md-5 w-100 mx-auto" style="max-width: 520px; position: relative;">

        <div class="text-center mb-4">
            <h1 class="fw-bold text-dark mb-1" style="font-size: 1.8rem;">E-Arsip</h1>
            <p class="text-secondary">Silakan login untuk melanjutkan</p>
        </div>

        <!-- Alert -->
        <?php if(isset($_GET['alert'])): ?>
            <?php if($_GET['alert'] == "gagal"): ?>
                <div class="alert alert-danger">Username / Password salah</div>
            <?php elseif($_GET['alert'] == "captcha"): ?>
                <div class="alert alert-warning">Captcha salah!</div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="proses-login.php" method="POST">

            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" required autocomplete="off">
                <label for="floatingInput">Username</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" required autocomplete="off">
                <label for="floatingInput">Password</label>
            </div>

           <!-- CAPTCHA -->
            <label class="fw-bold mb-2">Kode Captcha</label>

            <div class="row align-items-center mb-4">
                <!-- KODE CAPTCHA -->
                <div class="col-5">
                    <div class="bg-dark text-white text-center rounded fs-5 fw-bold user-select-none">
                        <?= $_SESSION['captcha']; ?>
                    </div>
                </div>

                <!-- INPUT CAPTCHA -->
                <div class="col-7">
                    <input 
                        type="text" 
                        name="captcha_input" 
                        class="form-control h-100"
                        placeholder="Masukkan captcha"
                        required
                    >
                </div>
            </div>


            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="z-index: 10; position: relative;">
                Login
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-secondary small">&copy; <?= date('Y'); ?> E-Arsip Digital</p>
        </div>

    </div>
</div>

</body>
</html>
