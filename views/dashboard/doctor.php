<?php Auth::requireRole('doctor'); ?>
<?php
$monthlyCounts = array_column($monthly ?? [], 'total', 'status');
$monthTotal = array_sum(array_map('intval', $monthlyCounts));
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
        <div class="col-lg-4 col-6"><div class="small-box bg-info"><div class="inner"><h3><?= e($monthTotal) ?></h3><p>This Month</p></div><div class="icon"><i class="fas fa-calendar"></i></div></div></div>
        <div class="col-lg-4 col-6"><div class="small-box bg-warning"><div class="inner"><h3><?= e($monthlyCounts['pending'] ?? 0) ?></h3><p>Pending</p></div><div class="icon"><i class="fas fa-hourglass-half"></i></div></div></div>
        <div class="col-lg-4 col-6"><div class="small-box bg-success"><div class="inner"><h3><?= e($monthlyCounts['completed'] ?? 0) ?></h3><p>Completed</p></div><div class="icon"><i class="fas fa-check-circle"></i></div></div></div>
      </div>

      <div class="row">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Today's Appointments</h3></div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead><tr><th>Time</th><th>Patient</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php foreach (($today ?? []) as $row): ?>
                  <tr>
                    <td><?= e(formatTime($row['appt_time'])) ?></td>
                    <td><?= e($row['patient_name']) ?></td>
                    <td><span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="<?= e(url('page=appointments&action=detail&id=' . $row['id'])) ?>"><i class="fas fa-eye"></i></a></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Upcoming</h3></div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                <?php foreach (($upcoming ?? []) as $row): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= e($row['patient_name']) ?><br><small><?= e(formatDate($row['appt_date']) . ' ' . formatTime($row['appt_time'])) ?></small></span>
                    <span class="badge badge-<?= e(statusBadge($row['status'])) ?>"><?= e($row['status']) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
