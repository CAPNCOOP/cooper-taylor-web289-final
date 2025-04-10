<?php if ($session->message() != ''): ?>
  <div class="popup-message">
    <?= h($session->message()); ?>
  </div>
<?php endif; ?>
