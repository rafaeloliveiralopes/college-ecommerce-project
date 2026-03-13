<?php
include('header.php');

$_SESSION = array();

if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
session_start();
admin_set_flash('success', 'Logout realizado com sucesso.');
admin_redirect('login.php');
?>
