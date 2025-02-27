<?php

// function require_login()
// {
//   global $session;
//   if (!$session->is_logged_in()) {
//     redirect_to(url_for('/index.php'));
//   }
// }

function require_login()
{
  global $session; // Ensure $session is actually available

  if (!isset($session)) {
    die("🚨 ERROR: \$session is NULL inside require_login()");
  }

  if (!$session->is_logged_in()) {
    die("🚨 ERROR: User is not logged in!");
    redirect_to('/login.php');
  }
}


function display_errors($errors = array())
{
  $output = '';
  if (!empty($errors)) {
    $output .= "<div class=\"errors\">";
    $output .= "Please fix the following errors:";
    $output .= "<ul>";
    foreach ($errors as $error) {
      $output .= "<li>" . h($error) . "</li>";
    }
    $output .= "</ul>";
    $output .= "</div>";
  }
  return $output;
}

function display_session_message()
{
  global $session;
  $msg = $session->message();
  if (isset($msg) && $msg != '') {
    $session->clear_message();
    return '<div id="message">' . h($msg) . '</div>';
  }
}
