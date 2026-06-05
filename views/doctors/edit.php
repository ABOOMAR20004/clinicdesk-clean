<?php Auth::requireRole('admin', 'doctor'); ?>
<?php
$available = array_map('trim', explode(',', $doctor['available_days'] ?? ''));
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
        <form method="post" action="<?= e(url('page=doctors&action=update')) ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= e($doctor['id']) ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <div class="form-group"><label>Name</label><input value="<?= e($doctor['name']) ?>" class="form-control" disabled></div>
                <div class="form-group">
                  <label>Specialization</label>
                  <select name="specialization_id" class="form-control" <?= Auth::role() === 'doctor' ? 'disabled' : '' ?>>
                    <?php foreach ($specializations as $item): ?>
                      <option value="<?= e($item['id']) ?>" <?= selected($doctor['specialization_id'], $item['id']) ?>><?= e($item['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (Auth::role() === 'doctor'): ?>
                    <input type="hidden" name="specialization_id" value="<?= e($doctor['specialization_id']) ?>">
                  <?php endif; ?>
                </div>
                <div class="form-group"><label>Consultation Fee</label><input type="number" step="0.01" name="consultation_fee" value="<?= e($doctor['consultation_fee']) ?>" class="form-control"></div>
                <div class="form-group"><label>Bio</label><textarea name="bio" rows="4" class="form-control"><?= e($doctor['bio']) ?></textarea></div>
                <div class="form-group">
                  <label>Available Days</label><br>
                  <?php foreach (dayOptions() as $day): ?>
                    <div class="icheck-primary d-inline mr-2">
                      <input type="checkbox" name="available_days[]" id="doctor_day_<?= e($day) ?>" value="<?= e($day) ?>" <?= checked(in_array($day, $available, true)) ?>>
                      <label for="doctor_day_<?= e($day) ?>"><?= e($day) ?></label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="col-md-4">
                <img src="<?= e(upload_url($doctor['avatar'])) ?>" class="img-fluid img-thumbnail mb-3" alt="">
                <div class="form-group">
                  <label>Profile Photo</label>
                  <div class="custom-file">
                    <input type="file" name="doctor_photo" class="custom-file-input" id="doctorPhoto" accept="image/png,image/jpeg">
                    <label class="custom-file-label" for="doctorPhoto">Choose image</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save</button>
            <a href="<?= e(url(Auth::role() === 'admin' ? 'page=doctors' : 'page=dashboard')) ?>" class="btn btn-secondary">Back</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
