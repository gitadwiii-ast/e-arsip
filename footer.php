<?php

?>

</div>
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