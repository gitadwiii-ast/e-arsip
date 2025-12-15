<?php include 'header.php'; ?>

<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="fw-bold">Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>!</h4>
            <p class="text-muted">
                Anda login sebagai <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
