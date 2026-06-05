<?php Auth::requireRole('admin', 'doctor', 'patient'); ?>
<?php
$role = Auth::role();
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
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Appointment</h3></div>
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Patient</dt><dd class="col-sm-8"><?= e($appointment['patient_name']) ?></dd>
                <dt class="col-sm-4">Doctor</dt><dd class="col-sm-8"><?= e($appointment['doctor_name']) ?>, <?= e($appointment['specialization']) ?></dd>
                <dt class="col-sm-4">Date</dt><dd class="col-sm-8"><?= e(formatDate($appointment['appt_date']) . ' ' . formatTime($appointment['appt_time'])) ?></dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge badge-<?= e(statusBadge($appointment['status'])) ?>"><?= e($appointment['status']) ?></span></dd>
                <dt class="col-sm-4">Reason</dt><dd class="col-sm-8"><?= e($appointment['reason']) ?></dd>
                <dt class="col-sm-4">Doctor Notes</dt><dd class="col-sm-8"><?= nl2br(e($appointment['doctor_notes'])) ?></dd>
              </dl>
            </div>
          </div>

          <?php if ($prescription): ?>
            <div class="card">
              <div class="card-header"><h3 class="card-title">Prescription</h3></div>
              <div class="card-body">
                <p><strong>Diagnosis:</strong> <?= nl2br(e($prescription['diagnosis'])) ?></p>
                <p><strong>Medications:</strong> <?= nl2br(e($prescription['medications'])) ?></p>
                <p><strong>Notes:</strong> <?= nl2br(e($prescription['notes'])) ?></p>
                <?php if ($prescription['file_path']): ?>
                  <a class="btn btn-outline-success" href="<?= e(url('page=prescriptions&action=download&id=' . $appointment['id'])) ?>"><i class="fas fa-file-download mr-1"></i> Download PDF</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="col-lg-5">
          <?php if (in_array($role, ['admin', 'doctor'], true)): ?>
            <div class="card">
              <div class="card-header"><h3 class="card-title">Update Status</h3></div>
              <form method="post" action="<?= e(url('page=appointments&action=status')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= e($appointment['id']) ?>">
                <div class="card-body">
                  <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                      <?php foreach (statusOptions() as $status): ?>
                        <option value="<?= e($status) ?>" <?= selected($appointment['status'], $status) ?>><?= e(ucfirst($status)) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group"><label>Doctor Notes</label><textarea name="doctor_notes" rows="4" class="form-control"><?= e($appointment['doctor_notes']) ?></textarea></div>
                </div>
                <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save</button></div>
              </form>
            </div>
          <?php endif; ?>

          <?php if ($role === 'doctor' && $appointment['status'] === 'completed' && !$prescription): ?>
            <a class="btn btn-success btn-block" href="<?= e(url('page=prescriptions&action=create&id=' . $appointment['id'])) ?>">
              <i class="fas fa-prescription mr-1"></i> Add Prescription
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
