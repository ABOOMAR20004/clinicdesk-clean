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
      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover js-data-table">
            <thead><tr><th>Doctor</th><th>Date</th><th>Diagnosis</th><th>PDF</th></tr></thead>
            <tbody>
            <?php foreach ($prescriptions as $row): ?>
              <tr>
                <td><?= e($row['doctor_name']) ?></td>
                <td><?= e(formatDate($row['appt_date'])) ?></td>
                <td><?= e(strlen($row['diagnosis']) > 80 ? substr($row['diagnosis'], 0, 80) . '...' : $row['diagnosis']) ?></td>
                <td>
                  <?php if ($row['file_path']): ?>
                    <a class="btn btn-sm btn-outline-success" href="<?= e(url('page=prescriptions&action=download&id=' . $row['appointment_id'])) ?>" title="Download"><i class="fas fa-file-download"></i></a>
                  <?php else: ?>
                    <span class="badge badge-secondary">None</span>
                  <?php endif; ?>
                </td>
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
