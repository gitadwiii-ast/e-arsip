<?php

?>

</div>
<footer class="bg-white border-t border-gray-200 p-4">
    <div class="text-center text-gray-500 text-sm">
        &copy; <?php echo date('Y'); ?> E-Arsip Digital. All rights reserved.
    </div>
</footer>
<?php include 'modal-peminjaman.php'; ?>
<script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleDropdown(id) {
        document.getElementById(id).classList.toggle('d-none');
        document.getElementById('icon-' + id).classList.toggle('rotate');
    }
</script>

</body>

</html>