<?php
ob_start();
session_start();
include('../server/connection.php');

if (!function_exists('admin_redirect')) {
  function admin_redirect($path)
  {
    header("Location: $path");
    exit();
  }
}

if (!function_exists('ensure_admin_session')) {
  function ensure_admin_session()
  {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
      $_SESSION['admin_error'] = 'Faça login para acessar o dashboard.';
      admin_redirect('login.php');
    }
  }
}

if (!function_exists('admin_status_label')) {
  function admin_status_label($status)
  {
    $labels = array(
      'on_hold' => 'Em analise',
      'paid' => 'Pago',
      'shipped' => 'Enviado',
      'delivered' => 'Entregue',
    );

    return $labels[$status] ?? $status;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | eCommerce CMS</title>
  <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/dashboard/">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" crossorigin="anonymous">
  <style>
    body {
      background-color: #f5f7fb;
    }

    .admin-topbar {
      background: #1f2937;
      color: #fff;
      padding: 1rem 0;
      box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    }

    .admin-brand {
      font-size: 1.15rem;
      font-weight: 700;
      margin: 0;
    }

    .sidebar {
      min-height: calc(100vh - 72px);
      border-right: 1px solid #e5e7eb;
    }

    .sidebar .nav-link {
      color: #4b5563;
      font-weight: 600;
      border-radius: 0.5rem;
      margin-bottom: 0.35rem;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background: #111827;
      color: #fff;
    }

    .content-card {
      border: 0;
      border-radius: 1rem;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .login-wrapper {
      min-height: calc(100vh - 72px);
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>
  <header class="admin-topbar">
    <div class="container-fluid px-4">
      <div class="d-flex justify-content-between align-items-center">
        <p class="admin-brand">Admin</p>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) { ?>
          <span class="small">Administrador: <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        <?php } ?>
      </div>
    </div>
  </header>
