<?php Auth::requireRole('admin'); ?>
<?php
require dirname(__DIR__) . '/partials/header.php';
require dirname(__DIR__) . '/partials/navbar.php';
require dirname(__DIR__) . '/partials/sidebar.php';
?>
<div class="content-wrapper">
  <?php require dirname(__DIR__) . '/partials/content_header.php'; ?>
  <section class="content">
    <div class="container-fluid">
      <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
      <div class="card">
        <div class="card-header">
          <form method="get" class="form-inline">
            <input type="hidden" name="page" value="reports">
            <input type="date" name="start_date" value="<?= e($filters['start_date']) ?>" class="form-control mr-2 mb-2" required>
            <input type="date" name="end_date" value="<?= e($filters['end_date']) ?>" class="form-control mr-2 mb-2" required>
            <select name="doctor_id" class="form-control mr-2 mb-2">
              <option value="">All doctors</option>
              <?php foreach ($doctors as $doctor): ?>
                <option value="<?= e($doctor['id']) ?>" <?= selected($filters['doctor_id'], $doctor['id']) ?>><?= e($doctor['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <select name="status" class="form-control mr-2 mb-2">
              <option value="">All statuses</option>
              <?php foreach (statusOptions() as $status): ?>
                <option value="<?= e($status) ?>" <?= selected($filters['status'], $status) ?>><?= e(ucfirst($status)) ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-primary mr-2 mb-2"><i class="fas fa-filter"></i></button>
            <?php if ($rows): ?>
              <?php $exportParams = array_merge($_GET, ['export' => 'csv']); ?>
              <a class="btn btn-outline-success mb-2" href="<?= e(url(http_build_query($exportParams))) ?>"><i class="fas fa-file-csv mr-1"></i> CSV</a>
            <?php endif; ?>
          </form>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover js-data-table">
            <thead><tr><th>Patient</th><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Reason</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= e($row['patient_name']) ?></td>
                <td><?= e($row['doctor_name']) ?></td>
                <td><?= e($row['specialization']) ?></td>
                <td><?= e(formatDate($row['appt_date'])) ?></td>
                <td><?= e(formatTime($row['appt_time'])) ?></td>
                <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['reason']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if ($rows): ?>
              <tfoot>
                <tr>
                  <th colspan="7">
                    Total: <?= e(count($rows)) ?>
                    <?php foreach ($summary as $status => $total): ?>
                      <span class="badge badge-<?= e(statusBadge($status)) ?> ml-2"><?= e($status . ': ' . $total) ?></span>
                    <?php endforeach; ?>
                  </th>
                </tr>
              </tfoot>
            <?php endif; ?>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
