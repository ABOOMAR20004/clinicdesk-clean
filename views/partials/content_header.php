<?php
$headerTitle = $headerTitle ?? $pageTitle ?? APP_NAME;
$breadcrumbTitle = $breadcrumbTitle ?? $headerTitle;
$headerAction = $headerAction ?? '';
?>
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6 d-flex align-items-center">
        <h1 class="m-0"><?= e($headerTitle) ?></h1>
        <?php if ($headerAction !== ''): ?>
          <div class="ml-3"><?= $headerAction ?></div>
        <?php endif; ?>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= e(url('page=dashboard')) ?>">Dashboard</a></li>
          <li class="breadcrumb-item active"><?= e($breadcrumbTitle) ?></li>
        </ol>
      </div>
    </div>
  </div>
</section>
<?php unset($headerTitle, $breadcrumbTitle, $headerAction); ?>
