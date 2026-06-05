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
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover js-data-table">
            <thead><tr><th>Photo</th><th>Name</th><th>Specialization</th><th>Fee</th><th>Available</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($doctors as $doctor): ?>
              <tr>
                <td style="width:56px"><img src="<?= e(upload_url($doctor['avatar'])) ?>" class="img-circle elevation-1" width="40" height="40" alt=""></td>
                <td><?= e($doctor['name']) ?><br><small><?= e($doctor['email']) ?></small></td>
                <td><?= e($doctor['specialization']) ?></td>
                <td><?= e(number_format((float) $doctor['consultation_fee'], 2)) ?></td>
                <td><?= e($doctor['available_days']) ?></td>
                <td><a href="<?= e(url('page=doctors&action=edit&id=' . $doctor['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a></td>
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
