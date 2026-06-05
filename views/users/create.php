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
      <form method="post" action="<?= e(url('page=users&action=store')) ?>">
        <?= csrf_field() ?>
        <div class="row">
          <div class="col-lg-7">
            <div class="card">
              <div class="card-header"><h3 class="card-title">Account</h3></div>
              <div class="card-body">
                <div class="form-group"><label>Name</label><input name="name" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="form-group"><label>Temporary Password</label><input type="password" name="password" class="form-control" minlength="8" required></div>
                <div class="form-group"><label>Phone</label><input name="phone" class="form-control"></div>
                <div class="form-group">
                  <label>Role</label>
                  <select name="role" id="roleSelect" class="form-control">
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="card" id="doctorFields">
              <div class="card-header"><h3 class="card-title">Doctor Record</h3></div>
              <div class="card-body">
                <div class="form-group">
                  <label>Specialization</label>
                  <select name="specialization_id" class="form-control">
                    <?php foreach ($specializations as $item): ?>
                      <option value="<?= e($item['id']) ?>"><?= e($item['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group"><label>Consultation Fee</label><input type="number" step="0.01" name="consultation_fee" class="form-control" value="0.00"></div>
                <div class="form-group"><label>Bio</label><textarea name="bio" class="form-control" rows="3"></textarea></div>
                <div class="form-group">
                  <label>Available Days</label><br>
                  <?php foreach (dayOptions() as $day): ?>
                    <div class="icheck-primary d-inline mr-2">
                      <input type="checkbox" name="available_days[]" id="day_<?= e($day) ?>" value="<?= e($day) ?>" <?= checked(in_array($day, ['Sun', 'Mon', 'Tue', 'Wed', 'Thu'], true)) ?>>
                      <label for="day_<?= e($day) ?>"><?= e($day) ?></label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <button class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i> Save User</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const role = document.getElementById('roleSelect');
  const panel = document.getElementById('doctorFields');
  const sync = () => panel.style.display = role.value === 'doctor' ? '' : 'none';
  role.addEventListener('change', sync);
  sync();
});
</script>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
