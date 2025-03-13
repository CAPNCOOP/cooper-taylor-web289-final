<?php
if (isset($_SESSION['message'])): ?>
  <div id="feedback-popup" class="feedback-popup">
    <?= htmlspecialchars($_SESSION['message']) ?>
  </div>
<?php
  unset($_SESSION['message']);
endif;
?>
