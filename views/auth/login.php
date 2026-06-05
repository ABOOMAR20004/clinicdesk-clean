<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?> | Login</title>
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/plugins/fontawesome-free/css/all.min.css')) ?>">
  <link rel="stylesheet" href="<?= e(asset('assets/adminlte/dist/css/adminlte.min.css')) ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <i class="fas fa-clinic-medical mr-2"></i><b>Clinic</b>Desk
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
      <form method="post" action="<?= e(url('page=auth&action=authenticate')) ?>">
        <?= csrf_field() ?>
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fas fa-sign-in-alt mr-1"></i> Sign In
        </button>
      </form>
    </div>
  </div>
</div>
<script src="<?= e(asset('assets/adminlte/plugins/jquery/jquery.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('assets/adminlte/dist/js/adminlte.min.js')) ?>"></script>
</body>
</html>
