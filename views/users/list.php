<?php Auth::requireRole('admin'); ?>
<?php
require dirname(__DIR__) . '/partials/header.php';
require dirname(__DIR__) . '/partials/navbar.php';
require dirname(__DIR__) . '/partials/sidebar.php';
?>
<div class="content-wrapper">
  <?php
  $headerAction = '<a href="' . e(url('page=users&action=create')) . '" class="btn btn-primary"><i class="fas fa-user-plus mr-1"></i> New User</a>';
  require dirname(__DIR__) . '/partials/content_header.php';
  ?>
  <section class="content">
    <div class="container-fluid">
      <?php require dirname(__DIR__) . '/partials/alerts.php'; ?>
      <div class="card">
        <div class="card-header">
          <form class="form-inline" method="get">
            <input type="hidden" name="page" value="users">
            <select name="role" class="form-control mr-2">
              <option value="">All roles</option>
              <?php foreach (['admin', 'doctor', 'patient'] as $item): ?>
                <option value="<?= e($item) ?>" <?= selected($role, $item) ?>><?= e(ucfirst($item)) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="search" name="q" value="<?= e($search) ?>" class="form-control mr-2" placeholder="Name or email">
            <button class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
          </form>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover js-data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $row): ?>
              <tr>
                <td><?= e($row['name']) ?></td>
                <td><?= e($row['email']) ?></td>
                <td><span class="badge badge-<?= e(roleBadge($row['role'])) ?>"><?= e($row['role']) ?></span></td>
                <td><?= (int) $row['is_active'] === 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' ?></td>
                <td><?= e(formatDate($row['created_at'])) ?></td>
                <td class="text-nowrap">
                  <a href="<?= e(url('page=users&action=edit&id=' . $row['id'])) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                  <form method="post" action="<?= e(url('page=users&action=toggle')) ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                    <button class="btn btn-sm btn-outline-warning" title="Activate or deactivate"><i class="fas fa-power-off"></i></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer"><?php require dirname(__DIR__) . '/partials/pagination.php'; ?></div>
      </div>
    </div>
  </section>
</div>
<?php require dirname(__DIR__) . '/partials/footer.php'; ?>
