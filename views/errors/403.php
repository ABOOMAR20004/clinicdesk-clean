<?php
if (Auth::check()) {
    require dirname(__DIR__) . '/partials/header.php';
    require dirname(__DIR__) . '/partials/navbar.php';
    require dirname(__DIR__) . '/partials/sidebar.php';
}
?>
<?php if (Auth::check()): ?>
<div class="content-wrapper">
  <section class="content pt-4">
    <div class="container-fluid">
      <div class="error-page">
        <h2 class="headline text-warning">403</h2>
        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> Forbidden</h3>
          <p>You do not have permission to access this page.</p>
          <a href="<?= e(url('page=dashboard')) ?>" class="btn btn-primary">Dashboard</a>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
<?php else: ?>
<p>Forbidden</p>
<?php endif; ?>
