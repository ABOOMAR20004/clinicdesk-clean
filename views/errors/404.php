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
        <h2 class="headline text-danger">404</h2>
        <div class="error-content">
          <h3><i class="fas fa-exclamation-circle text-danger"></i> Not Found</h3>
          <p>The requested page could not be found.</p>
          <a href="<?= e(url('page=dashboard')) ?>" class="btn btn-primary">Dashboard</a>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
<?php else: ?>
<p>Not Found</p>
<?php endif; ?>
