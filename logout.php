<?php
require_once 'private/initialize.php';

$goodbye_name = $session->username ?? 'User';
$session->logout(); // Clean sweep using our Session class

$session->message("✅ Goodbye, $goodbye_name! You have been logged out.");
redirect_to('login.php');
