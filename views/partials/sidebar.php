<?php $role = Auth::role(); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= e(url('page=dashboard')) ?>" class="brand-link">
    <i class="fas fa-clinic-medical brand-image ml-2 mt-1"></i>
    <span class="brand-text font-weight-light"><?= e(APP_NAME) ?></span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= e(upload_url((new UserModel())->findById(Auth::id())['avatar'] ?? null)) ?>" class="img-circle elevation-2" alt="">
      </div>
      <div class="info">
        <a href="<?= e(url('page=users&action=profile')) ?>" class="d-block"><?= e($user['name'] ?? '') ?></a>
        <span class="badge badge-<?= e(roleBadge($role)) ?>"><?= e(ucfirst($role)) ?></span>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <li class="nav-item">
          <a href="<?= e(url('page=dashboard')) ?>" class="nav-link <?= e(activeMenu('dashboard')) ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
          </a>
        </li>

        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a href="<?= e(url('page=users')) ?>" class="nav-link <?= e(activeMenu('users', 'index')) ?>">
              <i class="nav-icon fas fa-users"></i><p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=doctors')) ?>" class="nav-link <?= e(activeMenu('doctors')) ?>">
              <i class="nav-icon fas fa-user-md"></i><p>Doctors</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=specializations')) ?>" class="nav-link <?= e(activeMenu('specializations')) ?>">
              <i class="nav-icon fas fa-stethoscope"></i><p>Specializations</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=appointments')) ?>" class="nav-link <?= e(activeMenu('appointments')) ?>">
              <i class="nav-icon fas fa-calendar-check"></i><p>Appointments</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=reports')) ?>" class="nav-link <?= e(activeMenu('reports')) ?>">
              <i class="nav-icon fas fa-file-csv"></i><p>Reports</p>
            </a>
          </li>
        <?php elseif ($role === 'doctor'): ?>
          <li class="nav-item">
            <a href="<?= e(url('page=appointments')) ?>" class="nav-link <?= e(activeMenu('appointments')) ?>">
              <i class="nav-icon fas fa-calendar-day"></i><p>My Schedule</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=doctors&action=edit')) ?>" class="nav-link <?= e(activeMenu('doctors')) ?>">
              <i class="nav-icon fas fa-id-card"></i><p>Doctor Profile</p>
            </a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="<?= e(url('page=appointments&action=book')) ?>" class="nav-link <?= e(activeMenu('appointments', 'book')) ?>">
              <i class="nav-icon fas fa-calendar-plus"></i><p>Book Appointment</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=appointments')) ?>" class="nav-link <?= e(activeMenu('appointments', 'index')) ?>">
              <i class="nav-icon fas fa-calendar-alt"></i><p>My Appointments</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= e(url('page=prescriptions')) ?>" class="nav-link <?= e(activeMenu('prescriptions')) ?>">
              <i class="nav-icon fas fa-prescription-bottle-alt"></i><p>Prescriptions</p>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</aside>
