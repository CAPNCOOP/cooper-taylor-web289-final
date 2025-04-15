<?php

/**
 * Redirects to the homepage if no user is logged in.
 *
 * @return void
 */
function require_login()
{
  global $session;
  if (!$session->is_logged_in()) {
    redirect_to(url_for('/index.php'));
  }
}
