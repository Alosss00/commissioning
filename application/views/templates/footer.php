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

<!-- 6. Patch: nonaktifkan Simple DataTables bawaan NiceAdmin (konflik) -->
<script src="<?= base_url('assets/js/main.patch.js') ?>"></script>

</body>

</html>