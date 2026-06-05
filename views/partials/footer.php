  <footer class="main-footer">
    <strong>&copy; <?= e(date('Y')) ?> <?= e(APP_NAME) ?>.</strong>
    <span class="float-right d-none d-sm-inline">Clinic Management Dashboard</span>
  </footer>
</div>
<script src="<?= e(asset('assets/adminlte/plugins/jquery/jquery.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/datatables/jquery.dataTables.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/chart.js/Chart.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/dist/js/adminlte.min.js')) ?>"></script>
<script>
$(function () {
  $('.js-data-table').DataTable({ paging: false, searching: true, ordering: true, info: false, responsive: true, autoWidth: false });
  bsCustomFileInput.init();
  $('[title]').tooltip();
});
</script>
<?php if (!empty($chartRows)): ?>
<script>
$(function () {
  const canvas = document.getElementById('appointmentsChart');
  if (!canvas) return;
  const rows = <?= json_encode($chartRows, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
  new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels: rows.map(row => row.appt_date),
      datasets: [{ label: 'Appointments', data: rows.map(row => Number(row.total)), backgroundColor: '#17a2b8' }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] } }
  });
});
</script>
<?php endif; ?>
</body>
</html>
