<?php
if (!empty($_SESSION['message'])) {
  echo "<div class='feedback-popup'>" . $_SESSION['message'] . "</div>";
  unset($_SESSION['message']); // Clear session message after displaying
} elseif (!empty($_GET['logout_message'])) {
  echo "<div class='feedback-popup'>" . htmlspecialchars($_GET['logout_message']) . "</div>";
}
