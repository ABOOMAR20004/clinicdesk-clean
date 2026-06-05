<?php Auth::requireRole('patient'); ?>
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
      <?php if (!empty($stats['next'])): ?>
        <div class="callout callout-info">
          <h5>Next Appointment</h5>
          <p class="mb-0"><?= e($stats['next']['doctor_name']) ?>, <?= e(formatDate($stats['next']['appt_date']) . ' at ' . formatTime($stats['next']['appt_time'])) ?></p>
        </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-lg-4 col-6"><div class="small-box bg-info"><div class="inner"><h3><?= e($stats['active']) ?></h3><p>Active Appointments</p></div><div class="icon"><i class="fas fa-calendar-check"></i></div></div></div>
        <div class="col-lg-4 col-6"><div class="small-box bg-success"><div class="inner"><h3><?= e($stats['completed']) ?></h3><p>Completed</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
        <div class="col-lg-4 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?= e($prescriptionCount) ?></h3><p>Prescriptions</p></div><div class="icon"><i class="fas fa-file-medical"></i></div></div></div>
      </div>
      <div class="row">
        <div class="col-md-3 col-6">
          <a href="<?= e(url('page=appointments&action=book')) ?>" class="btn btn-app btn-block">
            <i class="fas fa-calendar-plus"></i> Book
          </a>
        </div>
        <div class="col-md-3 col-6">
          <a href="<?= e(url('page=appointments')) ?>" class="btn btn-app btn-block">
            <i class="fas fa-list"></i> Appointments
          </a>
        </div>
        <div class="col-md-3 col-6">
          <a href="<?= e(url('page=prescriptions')) ?>" class="btn btn-app btn-block">
            <i class="fas fa-file-medical"></i> Prescriptions
          </a>
        </div>
        <div class="col-md-3 col-6">
          <a href="<?= e(url('page=users&action=profile')) ?>" class="btn btn-app btn-block">
            <i class="fas fa-user-circle"></i> Profile
          </a>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><h3 class="card-title">Active Appointments</h3></div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover">
            <thead><tr><th>Doctor</th><th>Date</th><th>Status</th><th>Reason</th></tr></thead>
            <tbody>
            <?php foreach ($stats['activeList'] as $row): ?>
              <tr>
                <td><?= e($row['doctor_name']) ?></td>
                <td><?= e(formatDate($row['appt_date']) . ' ' . formatTime($row['appt_time'])) ?></td>
                <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['reason']) ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
