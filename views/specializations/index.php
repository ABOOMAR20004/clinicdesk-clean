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
      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Add Specialization</h3></div>
            <form method="post" action="<?= e(url('page=specializations&action=store')) ?>">
              <?= csrf_field() ?>
              <div class="card-body"><div class="form-group"><label>Name</label><input name="name" class="form-control" required></div></div>
              <div class="card-footer"><button class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add</button></div>
            </form>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header"><h3 class="card-title">Current Specializations</h3></div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead><tr><th>Name</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($specializations as $item): ?>
                  <tr>
                    <td><?= e($item['name']) ?></td>
                    <td class="text-right">
                      <form method="post" action="<?= e(url('page=specializations&action=delete')) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
