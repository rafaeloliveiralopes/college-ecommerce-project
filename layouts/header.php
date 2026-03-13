<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$is_user_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$current_user_id = (int) ($_SESSION['user_id'] ?? 0);

if (!function_exists('store_currency')) {
  function store_currency($value)
  {
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
  }
}

if (!function_exists('store_slugify')) {
  function store_slugify($value)
  {
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $value);
    $value = strtolower($value ?: '');
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);

    return trim((string) $value, '-');
  }
}

if (!function_exists('store_image_url')) {
  function store_image_url($filename, $product_name = '')
  {
    $base_dir = dirname(__DIR__) . '/assets/imgs/';
    $base_url = 'assets/imgs/';
    $filename = trim((string) $filename);

    if ($filename !== '' && file_exists($base_dir . $filename)) {
      return $base_url . $filename;
    }

    $product_slug = store_slugify($product_name);

    if ($product_slug !== '' && is_dir($base_dir)) {
      foreach (scandir($base_dir) as $file) {
        if ($file === '.' || $file === '..') {
          continue;
        }

        if (strpos(store_slugify(pathinfo($file, PATHINFO_FILENAME)), $product_slug) === 0) {
          return $base_url . $file;
        }
      }
    }

    return $base_url . 'logo.svg';
  }
}

$current_page = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>eCommerce CMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous">
  <link href="assets/css/style.css?v=9" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="index.php" aria-label="Voltar para a página inicial">
        <img src="assets/imgs/logo.svg?v=2" alt="Logo da loja" class="logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegacao">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav nav-buttons mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>" aria-current="<?php echo $current_page === 'index.php' ? 'page' : 'false'; ?>" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo in_array($current_page, array('products.php', 'single_product.php'), true) ? 'active' : ''; ?>" href="products.php">Produtos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php#home">Blog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php#footer-contact">Fale Conosco</a>
          </li>
          <li class="nav-item icon-item">
            <a class="nav-link" href="#" aria-label="Carrinho de compras">
              <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item icon-item">
            <a
              class="nav-link <?php echo in_array($current_page, array('login.php', 'register.php', 'account.php'), true) ? 'active' : ''; ?>"
              href="<?php echo $is_user_logged_in ? 'account.php?user_id=' . $current_user_id : 'login.php'; ?>"
              aria-label="<?php echo $is_user_logged_in ? 'Minha conta' : 'Login do usuario'; ?>"
            >
              <i class="fa fa-user" aria-hidden="true"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
