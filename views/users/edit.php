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
      <div class="card">
        <form method="post" action="<?= e(url('page=users&action=update')) ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= e($user['id']) ?>">
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <div class="form-group"><label>Name</label><input name="name" value="<?= e($user['name']) ?>" class="form-control" required></div>
                <div class="form-group"><label>Email</label><input value="<?= e($user['email']) ?>" class="form-control" disabled></div>
                <div class="form-group"><label>Phone</label><input name="phone" value="<?= e($user['phone']) ?>" class="form-control"></div>
                <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" minlength="8"></div>
              </div>
              <div class="col-md-4">
                <img src="<?= e(upload_url($user['avatar'])) ?>" class="img-fluid img-thumbnail mb-3" alt="">
                <div class="form-group">
                  <label>Avatar</label>
                  <div class="custom-file">
                    <input type="file" name="avatar" class="custom-file-input" id="editAvatar" accept="image/png,image/jpeg">
                    <label class="custom-file-label" for="editAvatar">Choose image</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save</button>
            <a href="<?= e(url('page=users')) ?>" class="btn btn-secondary">Back</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
