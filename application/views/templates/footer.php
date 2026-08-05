</div>
<footer id="footer" class="footer">
    <div class="copyright text-center small text-muted">
        &copy; Copyright <strong><span>TACTIC</span></strong>. All Rights Reserved
    </div>
</footer><!-- End Footer -->



<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


<!-- 1. jQuery -->


<!-- 3. jQuery Plugins -->
<script src="<?= base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= base_url('assets/js/sweetalert2.all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/js/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/flatpickr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/nprogress.min.js') ?>"></script>

<!-- 4. Chart libraries (standalone, tidak butuh jQuery) -->
<script src="<?= base_url('assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/echarts/echarts.min.js') ?>"></script>


<!-- 5. NiceAdmin main.js TERAKHIR -->
<!-- PENTING: simple-datatables.js di-HAPUS karena konflik dengan jQuery DataTables -->
<script src="<?= base_url('assets/js/main.js') ?>"></script>

<!-- Global JS Helper Tipe Akses Badge -->
<script>
function renderBadgeTipeAkses(v) {
    if (!v) return '<span class="text-muted small">—</span>';
    var key = String(v).toLowerCase().trim();
    if (key === 'mining' || (key.indexOf('mining') >= 0 && key.indexOf('non') < 0)) {
        return '<span class="badge bg-danger text-white">MINING ACCESS</span>';
    } else if (key.indexOf('non') >= 0 || key === 'non_mining') {
        return '<span class="badge bg-success text-white">NON MINING</span>';
    } else if (key.indexOf('underground') >= 0) {
        return '<span class="badge bg-secondary text-white">UNDERGROUND</span>';
    }
    return '<span class="badge bg-secondary text-white">' + String(v).toUpperCase() + '</span>';
}
</script>
</body>

</html>