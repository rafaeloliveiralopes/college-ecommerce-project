<?php
include('connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function redirect_to_order_details($order_id)
{
  if ($order_id > 0) {
    header('Location: ../order_details.php?order_id=' . $order_id);
    exit;
  }

  $user_id = (int) ($_SESSION['user_id'] ?? 0);
  header('Location: ../account.php?user_id=' . $user_id);
  exit;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  $_SESSION['order_details_error'] = 'Faca login para confirmar o pagamento do pedido.';
  header('Location: ../login.php');
  exit;
}

$session_user_id = (int) ($_SESSION['user_id'] ?? 0);
$order_id = (int) ($_GET['order_id'] ?? 0);
$transaction_id = trim((string) ($_GET['transaction_id'] ?? ''));

if ($order_id <= 0 || $transaction_id === '') {
  $_SESSION['order_details_error'] = 'Nao foi possivel identificar o pagamento realizado.';
  redirect_to_order_details($order_id);
}

$order_query = mysqli_prepare($conn, 'SELECT order_id, order_status FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1');
$order = null;

if ($order_query) {
  mysqli_stmt_bind_param($order_query, 'ii', $order_id, $session_user_id);
  mysqli_stmt_execute($order_query);
  $order_result = mysqli_stmt_get_result($order_query);
  $order = mysqli_fetch_assoc($order_result) ?: null;
  mysqli_stmt_close($order_query);
}

if (!$order) {
  $_SESSION['order_details_error'] = 'Pedido nao encontrado para confirmar o pagamento.';
  redirect_to_order_details($order_id);
}

if ($order['order_status'] === 'paid') {
  $_SESSION['order_details_success'] = 'Este pedido ja estava marcado como pago.';
  redirect_to_order_details($order_id);
}

mysqli_begin_transaction($conn);

try {
  $payment_check = mysqli_prepare($conn, 'SELECT payment_id FROM payments WHERE order_id = ? OR transaction_id = ? LIMIT 1');

  if (!$payment_check) {
    throw new Exception('Nao foi possivel validar o pagamento do pedido.');
  }

  mysqli_stmt_bind_param($payment_check, 'is', $order_id, $transaction_id);
  mysqli_stmt_execute($payment_check);
  $payment_result = mysqli_stmt_get_result($payment_check);
  $existing_payment = mysqli_fetch_assoc($payment_result) ?: null;
  mysqli_stmt_close($payment_check);

  if ($existing_payment) {
    $update_paid_order = mysqli_prepare($conn, 'UPDATE orders SET order_status = ? WHERE order_id = ? AND user_id = ?');

    if (!$update_paid_order) {
      throw new Exception('Nao foi possivel atualizar o status do pedido.');
    }

    $paid_status = 'paid';
    mysqli_stmt_bind_param($update_paid_order, 'sii', $paid_status, $order_id, $session_user_id);
    mysqli_stmt_execute($update_paid_order);
    mysqli_stmt_close($update_paid_order);

    mysqli_commit($conn);
    $_SESSION['order_details_success'] = 'Pagamento reconhecido com sucesso.';
    redirect_to_order_details($order_id);
  }

  $update_order = mysqli_prepare($conn, 'UPDATE orders SET order_status = ? WHERE order_id = ? AND user_id = ?');

  if (!$update_order) {
    throw new Exception('Nao foi possivel atualizar o pedido para pago.');
  }

  $paid_status = 'paid';
  mysqli_stmt_bind_param($update_order, 'sii', $paid_status, $order_id, $session_user_id);

  if (!mysqli_stmt_execute($update_order)) {
    mysqli_stmt_close($update_order);
    throw new Exception('Nao foi possivel atualizar o status do pedido.');
  }

  mysqli_stmt_close($update_order);

  $insert_payment = mysqli_prepare($conn, 'INSERT INTO payments (order_id, user_id, transaction_id) VALUES (?, ?, ?)');

  if (!$insert_payment) {
    throw new Exception('Nao foi possivel registrar o pagamento no banco de dados.');
  }

  mysqli_stmt_bind_param($insert_payment, 'iis', $order_id, $session_user_id, $transaction_id);

  if (!mysqli_stmt_execute($insert_payment)) {
    mysqli_stmt_close($insert_payment);
    throw new Exception('Nao foi possivel salvar a transacao do PayPal.');
  }

  mysqli_stmt_close($insert_payment);
  mysqli_commit($conn);

  $_SESSION['order_details_success'] = 'Pagamento confirmado com sucesso.';
  redirect_to_order_details($order_id);
} catch (Exception $exception) {
  mysqli_rollback($conn);
  $_SESSION['order_details_error'] = $exception->getMessage();
  redirect_to_order_details($order_id);
}
