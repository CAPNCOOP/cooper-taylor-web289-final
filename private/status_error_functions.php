<?php

/**
 * The function `require_login` checks if a user is logged in and redirects to the index page if not.
 */
function require_login()
{
  global $session;
  if (!$session->is_logged_in()) {
    redirect_to(url_for('/index.php'));
  }
}
