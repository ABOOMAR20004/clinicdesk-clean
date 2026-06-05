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
        <form method="post" action="<?= e(url('page=appointments&action=store')) ?>">
          <?= csrf_field() ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Doctor</label>
                  <select name="doctor_id" id="doctorSelect" class="form-control" required>
                    <option value="">Select doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                      <option value="<?= e($doctor['id']) ?>" data-days="<?= e($doctor['available_days']) ?>" <?= selected($selectedDoctor, $doctor['id']) ?>>
                        <?= e($doctor['name'] . ' - ' . $doctor['specialization']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group"><label>Available Days</label><input id="availableDays" class="form-control" disabled></div>
                <div class="form-group"><label>Reason</label><input name="reason" maxlength="255" class="form-control"></div>
              </div>
              <div class="col-md-6">
                <div class="form-group"><label>Date</label><input type="date" name="appt_date" min="<?= e(date('Y-m-d')) ?>" class="form-control" required></div>
                <div class="form-group">
                  <label>Time Slot</label>
                  <select name="appt_time" class="form-control" required>
                    <?php foreach (timeSlots() as $slot): ?>
                      <option value="<?= e($slot) ?>"><?= e($slot) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-calendar-plus mr-1"></i> Book</button></div>
        </form>
      </div>
    </div>
  </section>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const select = document.getElementById('doctorSelect');
  const days = document.getElementById('availableDays');
  const sync = () => days.value = select.options[select.selectedIndex]?.dataset.days || '';
  select.addEventListener('change', sync);
  sync();
});
</script>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
