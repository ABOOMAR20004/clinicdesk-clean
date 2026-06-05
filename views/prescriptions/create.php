<?php Auth::requireRole('doctor'); ?>
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
          <h3 class="card-title"><?= e($appointment['patient_name']) ?>, <?= e(formatDate($appointment['appt_date'])) ?></h3>
        </div>
        <form method="post" action="<?= e(url('page=prescriptions&action=store')) ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="appointment_id" value="<?= e($appointment['id']) ?>">
          <div class="card-body">
            <div class="form-group"><label>Diagnosis</label><textarea name="diagnosis" rows="4" class="form-control" required></textarea></div>
            <div class="form-group"><label>Medications</label><textarea name="medications" rows="4" class="form-control" required></textarea></div>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="3" class="form-control"></textarea></div>
            <div class="form-group">
              <label>Prescription PDF</label>
              <div class="custom-file">
                <input type="file" name="prescription_file" class="custom-file-input" id="prescriptionFile" accept="application/pdf">
                <label class="custom-file-label" for="prescriptionFile">Choose PDF</label>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save Prescription</button>
            <a href="<?= e(url('page=appointments&action=detail&id=' . $appointment['id'])) ?>" class="btn btn-secondary">Back</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
