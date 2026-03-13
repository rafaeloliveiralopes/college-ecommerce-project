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
      $_SESSION['admin_error'] = 'Faça login para acessar o painel.';
      admin_redirect('login.php');
    }
  }
}

if (!function_exists('admin_status_label')) {
  function admin_status_label($status)
  {
    $labels = array(
      'on_hold' => 'Em análise',
      'paid' => 'Pago',
      'shipped' => 'Enviado',
      'delivered' => 'Entregue',
    );

    return $labels[$status] ?? $status;
  }
}

if (!function_exists('admin_set_flash')) {
  function admin_set_flash($type, $message)
  {
    $_SESSION['admin_flash'] = array(
      'type' => $type,
      'message' => $message,
    );
  }
}

if (!function_exists('admin_get_flash')) {
  function admin_get_flash()
  {
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);

    return $flash;
  }
}

if (!function_exists('admin_alert_class')) {
  function admin_alert_class($type)
  {
    $classes = array(
      'success' => 'alert-success',
      'danger' => 'alert-danger',
      'warning' => 'alert-warning',
      'info' => 'alert-info',
    );

    return $classes[$type] ?? 'alert-secondary';
  }
}

if (!function_exists('admin_format_currency')) {
  function admin_format_currency($value)
  {
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
  }
}

if (!function_exists('admin_paginate')) {
  function admin_paginate($conn, $table, $page, $items_per_page)
  {
    $safe_table = preg_replace('/[^a-z_]/i', '', $table);
    $total_result = mysqli_query($conn, "SELECT COUNT(*) AS total_items FROM {$safe_table}");
    $total_row = $total_result ? mysqli_fetch_assoc($total_result) : array('total_items' => 0);
    $total_items = (int) ($total_row['total_items'] ?? 0);
    $total_pages = max(1, (int) ceil($total_items / $items_per_page));
    $current_page = min(max(1, $page), $total_pages);
    $offset = ($current_page - 1) * $items_per_page;

    return array($total_items, $total_pages, $current_page, $offset);
  }
}

if (!function_exists('admin_render_pagination')) {
  function admin_render_pagination($base_path, $page, $total_pages)
  {
    if ($total_pages <= 1) {
      return;
    }
    ?>
    <nav class="mt-4" aria-label="Paginação">
      <ul class="pagination">
        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo htmlspecialchars($base_path . '?page=' . max(1, $page - 1)); ?>">Anterior</a>
        </li>

        <?php for ($current_page = 1; $current_page <= $total_pages; $current_page++) { ?>
          <li class="page-item <?php echo $current_page === $page ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo htmlspecialchars($base_path . '?page=' . $current_page); ?>"><?php echo $current_page; ?></a>
          </li>
        <?php } ?>

        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo htmlspecialchars($base_path . '?page=' . min($total_pages, $page + 1)); ?>">Próxima</a>
        </li>
      </ul>
    </nav>
    <?php
  }
}

if (!function_exists('admin_slugify_filename')) {
  function admin_slugify_filename($filename)
  {
    $filename = strtolower(pathinfo($filename, PATHINFO_FILENAME));
    $filename = preg_replace('/[^a-z0-9]+/', '-', $filename);
    $filename = trim($filename, '-');

    return $filename !== '' ? $filename : 'product';
  }
}

if (!function_exists('admin_upload_image')) {
  function admin_upload_image($file, &$error_message)
  {
    if (!isset($file) || !is_array($file)) {
      $error_message = 'Selecione uma imagem válida.';
      return null;
    }

    $upload_error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($upload_error !== UPLOAD_ERR_OK) {
      $upload_messages = array(
        UPLOAD_ERR_INI_SIZE => 'A imagem enviada excede o limite configurado no PHP.',
        UPLOAD_ERR_FORM_SIZE => 'A imagem enviada excede o limite permitido pelo formulário.',
        UPLOAD_ERR_PARTIAL => 'A imagem foi enviada apenas parcialmente. Tente novamente.',
        UPLOAD_ERR_NO_FILE => 'Selecione uma imagem válida.',
        UPLOAD_ERR_NO_TMP_DIR => 'A pasta temporária de upload do PHP não está disponível.',
        UPLOAD_ERR_CANT_WRITE => 'O servidor não conseguiu gravar a imagem temporária em disco.',
        UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o upload da imagem.',
      );

      $error_message = $upload_messages[$upload_error] ?? 'Não foi possível receber a imagem enviada.';
      return null;
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'svg', 'webp');

    if (!in_array($extension, $allowed_extensions, true)) {
      $error_message = 'Envie a imagem em um formato permitido: jpg, png, gif, svg ou webp.';
      return null;
    }

    $upload_dir = dirname(__DIR__) . '/assets/imgs/';
    $safe_name = admin_slugify_filename($file['name']) . '-' . uniqid() . '.' . $extension;
    $destination = $upload_dir . $safe_name;

    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
      $error_message = 'Não foi possível preparar a pasta de imagens.';
      return null;
    }

    if (!is_writable($upload_dir)) {
      @chmod($upload_dir, 0777);
    }

    if (!is_writable($upload_dir)) {
      $error_message = 'A pasta assets/imgs não está com permissão de escrita para o servidor.';
      return null;
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
      $error_message = 'Não foi possível enviar a imagem para o servidor.';
      return null;
    }

    return $safe_name;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel Administrativo | eCommerce CMS</title>
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

    .product-thumb {
      width: 52px;
      height: 52px;
      object-fit: cover;
      border-radius: 0.75rem;
      background: #e5e7eb;
      padding: 0.2rem;
    }

    .product-gallery-preview {
      width: 100%;
      max-width: 160px;
      aspect-ratio: 1 / 1;
      object-fit: cover;
      border-radius: 1rem;
      background: #e5e7eb;
      padding: 0.25rem;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>
<body>
  <header class="admin-topbar">
    <div class="container-fluid px-4">
      <div class="d-flex justify-content-between align-items-center">
        <p class="admin-brand">Painel Administrativo</p>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) { ?>
          <div class="d-flex align-items-center gap-3">
            <span class="small">Administrador: <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <a href="logout.php" class="text-white text-decoration-none small">Sair</a>
          </div>
        <?php } ?>
      </div>
    </div>
  </header>
