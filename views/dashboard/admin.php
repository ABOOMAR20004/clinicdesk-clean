<?php Auth::requireRole('admin'); ?>
<?php
$roleCounts = array_column($roleTotals, 'total', 'role');
$statusCounts = array_column($weekStatus, 'total', 'status');
require dirname(__DIR__) . '/partials/header.php';
require dirname(__DIR__) . '/partials/navbar.php';
require dirname(__DIR__) . '/partials/sidebar.php';
?>
<div class="content-wrapper">
  <?php require dirname(__DIR__) . '/partials/content_header.php'; ?>
  <section class="content">
    <div class="container-fluid">
      <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
      <div class="row">
        <?php foreach (['admin' => 'danger', 'doctor' => 'primary', 'patient' => 'success'] as $role => $color): ?>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-<?= e($color) ?>">
              <div class="inner"><h3><?= e($roleCounts[$role] ?? 0) ?></h3><p><?= e(ucfirst($role) . 's') ?></p></div>
              <div class="icon"><i class="fas fa-users"></i></div>
            </div>
          </div>
        <?php endforeach; ?>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner"><h3><?= e($todayTotal) ?></h3><p>Appointments Today</p></div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Recent Appointments</h3></div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $row): ?>
                  <tr>
                    <td><?= e($row['patient_name']) ?></td>
                    <td><?= e($row['doctor_name']) ?></td>
                    <td><?= e(formatDate($row['appt_date']) . ' ' . formatTime($row['appt_time'])) ?></td>
                    <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header"><h3 class="card-title">This Week</h3></div>
            <div class="card-body">
              <div class="row">
                <?php foreach (statusOptions() as $status): ?>
                  <div class="col-6 mb-3">
                    <div class="info-box mb-0">
                      <span class="info-box-icon bg-<?= e(statusBadge($status)) ?>"><i class="fas fa-notes-medical"></i></span>
                      <div class="info-box-content">
                        <span class="info-box-text"><?= e(ucfirst($status)) ?></span>
                        <span class="info-box-number"><?= e($statusCounts[$status] ?? 0) ?></span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><h3 class="card-title">Last 14 Days</h3></div>
            <div class="card-body"><div style="height:240px"><canvas id="appointmentsChart"></canvas></div></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
