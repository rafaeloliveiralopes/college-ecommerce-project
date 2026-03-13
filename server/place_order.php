<?php
include('connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function redirect_to_checkout()
{
  header('Location: ../checkout.php');
  exit;
}

function place_order_cart_summary()
{
  $total_price = 0.0;
  $total_quantity = 0;

  if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['total'] = 0;
    $_SESSION['quantity'] = 0;

    return array('total' => 0.0, 'quantity' => 0);
  }

  foreach ($_SESSION['cart'] as $product_id => $item) {
    if (!is_array($item)) {
      unset($_SESSION['cart'][$product_id]);
      continue;
    }

    $quantity = max(0, (int) ($item['product_quantity'] ?? 0));
    $price = (float) ($item['product_price'] ?? 0);

    if ($quantity === 0) {
      unset($_SESSION['cart'][$product_id]);
      continue;
    }

    $total_quantity += $quantity;
    $total_price += $price * $quantity;
  }

  $_SESSION['total'] = $total_price;
  $_SESSION['quantity'] = $total_quantity;

  return array(
    'total' => $total_price,
    'quantity' => $total_quantity,
  );
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../cart.php');
  exit;
}

$cart_summary = place_order_cart_summary();

if ($cart_summary['quantity'] === 0) {
  header('Location: ../index.php');
  exit;
}

$shipping_uf = strtoupper(trim((string) ($_POST['shipping_uf'] ?? '')));
$shipping_city = trim((string) ($_POST['shipping_city'] ?? ''));
$shipping_address = trim((string) ($_POST['shipping_address'] ?? ''));

$_SESSION['checkout_form'] = array(
  'shipping_uf' => $shipping_uf,
  'shipping_city' => $shipping_city,
  'shipping_address' => $shipping_address,
);

if ($shipping_uf === '' || $shipping_city === '' || $shipping_address === '') {
  $_SESSION['checkout_error'] = 'Preencha UF, cidade e endereco para continuar.';
  redirect_to_checkout();
}

if (strlen($shipping_uf) !== 2) {
  $_SESSION['checkout_error'] = 'Informe uma UF valida com 2 caracteres.';
  redirect_to_checkout();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
  $_SESSION['checkout_success'] = 'Voce precisa fazer <a href="login.php">login</a> para concluir o checkout.';
  redirect_to_checkout();
}

$user_id = (int) $_SESSION['user_id'];
$order_status = 'not paid';
$order_date = date('Y-m-d H:i:s');

mysqli_begin_transaction($conn);

try {
  $insert_order = mysqli_prepare($conn, 'INSERT INTO orders (order_cost, order_status, user_id, shipping_city, shipping_uf, shipping_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)');

  if (!$insert_order) {
    throw new Exception('Nao foi possivel preparar o pedido.');
  }

  mysqli_stmt_bind_param($insert_order, 'dsissss', $cart_summary['total'], $order_status, $user_id, $shipping_city, $shipping_uf, $shipping_address, $order_date);

  if (!mysqli_stmt_execute($insert_order)) {
    mysqli_stmt_close($insert_order);
    throw new Exception('Nao foi possivel salvar o pedido.');
  }

  $order_id = (int) mysqli_insert_id($conn);
  mysqli_stmt_close($insert_order);

  $insert_item = mysqli_prepare($conn, 'INSERT INTO order_items (order_id, product_id, user_id, qnt, order_date) VALUES (?, ?, ?, ?, ?)');

  if (!$insert_item) {
    throw new Exception('Nao foi possivel preparar os itens do pedido.');
  }

  $saved_items = 0;

  foreach ($_SESSION['cart'] as $item) {
    $product_id = (int) ($item['product_id'] ?? 0);
    $quantity = max(1, (int) ($item['product_quantity'] ?? 1));

    if ($product_id <= 0) {
      continue;
    }

    mysqli_stmt_bind_param($insert_item, 'iiiis', $order_id, $product_id, $user_id, $quantity, $order_date);

    if (!mysqli_stmt_execute($insert_item)) {
      mysqli_stmt_close($insert_item);
      throw new Exception('Nao foi possivel salvar os itens do pedido.');
    }

    $saved_items++;
  }

  if ($saved_items === 0) {
    mysqli_stmt_close($insert_item);
    throw new Exception('Nao foi possivel identificar itens validos para o pedido.');
  }

  mysqli_stmt_close($insert_item);
  mysqli_commit($conn);

  $_SESSION['order_id'] = $order_id;
  $_SESSION['account_success'] = 'Pedido #' . $order_id . ' realizado com sucesso.';
  unset($_SESSION['cart'], $_SESSION['total'], $_SESSION['quantity'], $_SESSION['checkout_form']);

  header('Location: ../account.php?user_id=' . $user_id);
  exit;
} catch (Exception $exception) {
  mysqli_rollback($conn);
  $_SESSION['checkout_error'] = $exception->getMessage();
  redirect_to_checkout();
}
