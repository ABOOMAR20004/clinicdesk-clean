<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Toggle navigation">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?= e(url('page=dashboard')) ?>" class="nav-link">Dashboard</a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?= e(url('page=users&action=profile')) ?>" class="nav-link">
        <i class="fas fa-user-circle mr-1"></i><?= e($user['name'] ?? '') ?>
      </a>
    </li>
    <li class="nav-item">
      <form method="post" action="<?= e(url('page=auth&action=logout')) ?>" class="m-0">
        <?= csrf_field() ?>
        <button class="btn btn-link nav-link" type="submit" title="Logout">
          <i class="fas fa-sign-out-alt"></i>
        </button>
      </form>
    </li>
  </ul>
</nav>
