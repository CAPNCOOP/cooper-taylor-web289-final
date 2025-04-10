<?php if (!empty($session->message())): ?>
  <div class="popup-message">
    <?= h($session->message()); ?>
    <?php $session->clear_message(); ?>
  </div>
<?php endif; ?>
