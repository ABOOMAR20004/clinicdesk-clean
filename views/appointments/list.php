<?php Auth::requireRole('admin', 'doctor', 'patient'); ?>
<?php
require dirname(__DIR__) . '/partials/header.php';
require dirname(__DIR__) . '/partials/navbar.php';
require dirname(__DIR__) . '/partials/sidebar.php';
?>
<div class="content-wrapper">
  <?php
  $headerTitle = $role === 'doctor' ? 'My Schedule' : $pageTitle;
  $headerAction = $role === 'patient'
      ? '<a href="' . e(url('page=appointments&action=book')) . '" class="btn btn-primary"><i class="fas fa-calendar-plus mr-1"></i> Book</a>'
      : '';
  require dirname(__DIR__) . '/partials/content_header.php';
  ?>
  <section class="content">
    <div class="container-fluid">
      <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>

      <?php if ($role === 'doctor'): ?>
        <div class="card">
          <div class="card-header"><h3 class="card-title">Today</h3></div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead><tr><th>Time</th><th>Patient</th><th>Reason</th><th>Status</th><th></th></tr></thead>
              <tbody>
              <?php foreach ($today as $row): ?>
                <tr>
                  <td><?= e(formatTime($row['appt_time'])) ?></td>
                  <td><?= e($row['patient_name']) ?></td>
                  <td><?= e($row['reason']) ?></td>
                  <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                  <td><a class="btn btn-sm btn-outline-primary" href="<?= e(url('page=appointments&action=detail&id=' . $row['id'])) ?>"><i class="fas fa-eye"></i></a></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <form method="get" class="form-inline">
            <input type="hidden" name="page" value="appointments">
            <select name="status" class="form-control mr-2 mb-2">
              <option value="">All statuses</option>
              <?php foreach (statusOptions() as $status): ?>
                <option value="<?= e($status) ?>" <?= selected($filters['status'], $status) ?>><?= e(ucfirst($status)) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if ($role === 'admin'): ?>
              <select name="doctor_id" class="form-control mr-2 mb-2">
                <option value="">All doctors</option>
                <?php foreach ($doctors as $doctor): ?>
                  <option value="<?= e($doctor['id']) ?>" <?= selected($filters['doctor_id'], $doctor['id']) ?>><?= e($doctor['name']) ?></option>
                <?php endforeach; ?>
              </select>
              <input name="patient_search" value="<?= e($filters['patient_search']) ?>" class="form-control mr-2 mb-2" placeholder="Patient">
            <?php endif; ?>
            <input type="date" name="start_date" value="<?= e($filters['start_date']) ?>" class="form-control mr-2 mb-2">
            <input type="date" name="end_date" value="<?= e($filters['end_date']) ?>" class="form-control mr-2 mb-2">
            <button class="btn btn-outline-primary mb-2" title="Filter"><i class="fas fa-filter"></i></button>
          </form>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover js-data-table">
            <thead>
              <tr>
                <?php if ($role !== 'patient'): ?><th>Patient</th><?php endif; ?>
                <?php if ($role !== 'doctor'): ?><th>Doctor</th><?php endif; ?>
                <th>Specialization</th><th>Date</th><th>Time</th><th>Status</th><th>Reason</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $row): ?>
              <tr>
                <?php if ($role !== 'patient'): ?><td><?= e($row['patient_name']) ?></td><?php endif; ?>
                <?php if ($role !== 'doctor'): ?><td><?= e($row['doctor_name']) ?></td><?php endif; ?>
                <td><?= e($row['specialization']) ?></td>
                <td><?= e(formatDate($row['appt_date'])) ?></td>
                <td><?= e(formatTime($row['appt_time'])) ?></td>
                <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['reason']) ?></td>
                <td class="text-nowrap">
                  <a class="btn btn-sm btn-outline-primary" href="<?= e(url('page=appointments&action=detail&id=' . $row['id'])) ?>" title="View"><i class="fas fa-eye"></i></a>
                  <?php if ($role === 'patient' && $row['status'] === 'pending'): ?>
                    <form method="post" action="<?= e(url('page=appointments&action=cancel')) ?>" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                      <button class="btn btn-sm btn-outline-danger" title="Cancel"><i class="fas fa-times"></i></button>
                    </form>
                  <?php endif; ?>
                  <?php if ($role === 'patient' && $row['status'] === 'completed' && $row['prescription_id']): ?>
                    <a class="btn btn-sm btn-outline-success" href="<?= e(url('page=prescriptions&action=download&id=' . $row['id'])) ?>" title="Download Prescription"><i class="fas fa-file-download"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer"><?php require dirname(__DIR__) . '/partials/pagination.php'; ?></div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
