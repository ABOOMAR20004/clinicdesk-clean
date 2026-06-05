<?php if (isset($paginator) && $paginator->totalPages() > 1): ?>
  <nav aria-label="Pagination">
    <ul class="pagination justify-content-end mb-0">
      <?php
      $params = $_GET;
      $current = $paginator->currentPage();
      $total = $paginator->totalPages();
      ?>
      <li class="page-item <?= $paginator->hasPrev() ? '' : 'disabled' ?>">
        <?php $params['p'] = max(1, $current - 1); ?>
        <a class="page-link" href="<?= e(url(http_build_query($params))) ?>">Previous</a>
      </li>
      <?php for ($i = 1; $i <= $total; $i++): ?>
        <?php $params['p'] = $i; ?>
        <li class="page-item <?= $i === $current ? 'active' : '' ?>">
          <a class="page-link" href="<?= e(url(http_build_query($params))) ?>"><?= e($i) ?></a>
        </li>
      <?php endfor; ?>
      <li class="page-item <?= $paginator->hasNext() ? '' : 'disabled' ?>">
        <?php $params['p'] = min($total, $current + 1); ?>
        <a class="page-link" href="<?= e(url(http_build_query($params))) ?>">Next</a>
      </li>
    </ul>
  </nav>
<?php endif; ?>
