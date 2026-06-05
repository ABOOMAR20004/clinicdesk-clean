<?php if (!empty($_SESSION['flash'])): ?>
  <?php foreach ($_SESSION['flash'] as $message): ?>
    <div class="alert alert-<?= e($message['type']) ?> alert-dismissible fade show">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <?= e($message['message']) ?>
    </div>
  <?php endforeach; ?>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
