<?php
include('server/connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function payment_status_label($status)
{
  $labels = array(
    'not paid' => 'Pendente de pagamento',
    'on_hold' => 'Em analise',
    'paid' => 'Pago',
    'shipped' => 'Enviado',
    'delivered' => 'Entregue',
  );

  return $labels[(string) $status] ?? (string) $status;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: login.php');
  exit;
}

$session_user_id = (int) ($_SESSION['user_id'] ?? 0);
$order_id = (int) ($_POST['order_id'] ?? $_GET['order_id'] ?? $_SESSION['order_id'] ?? 0);
$payment_success = (string) ($_SESSION['payment_success'] ?? '');
$payment_error = (string) ($_SESSION['payment_error'] ?? '');
unset($_SESSION['payment_success'], $_SESSION['payment_error']);

$order = null;
$error_message = '';
$warning_message = '';
$paypal_client_id = trim((string) project_env('PAYPAL_CLIENT_ID', ''));
$paypal_currency = trim((string) project_env('PAYPAL_CURRENCY', 'BRL'));
$paypal_currency = $paypal_currency !== '' ? strtoupper($paypal_currency) : 'BRL';

if ($order_id <= 0) {
  $error_message = 'Selecione um pedido valido para continuar com o pagamento.';
} else {
  $order_query = mysqli_prepare($conn, 'SELECT order_id, order_cost, order_status, shipping_city, shipping_uf, shipping_address, order_date FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1');

  if ($order_query) {
    mysqli_stmt_bind_param($order_query, 'ii', $order_id, $session_user_id);
    mysqli_stmt_execute($order_query);
    $order_result = mysqli_stmt_get_result($order_query);
    $order = mysqli_fetch_assoc($order_result) ?: null;
    mysqli_stmt_close($order_query);
  }

  if (!$order) {
    $error_message = 'Pedido nao encontrado para a sua conta.';
  } elseif ($order['order_status'] === 'paid') {
    $warning_message = 'Este pedido ja esta pago.';
  } elseif ($order['order_status'] !== 'not paid') {
    $error_message = 'Este pedido nao esta disponivel para pagamento no momento.';
  } elseif ($paypal_client_id === '') {
    $warning_message = 'Não podemos processar o pagamento no momento. Tente novamente mais tarde.';
  }
}

$amount = $order ? number_format((float) $order['order_cost'], 2, '.', '') : '0.00';
$status_class = $order ? strtolower(str_replace(' ', '-', (string) $order['order_status'])) : 'not-paid';

include('layouts/header.php');
?>

<section class="payment-section py-5">
  <div class="container">
    <div class="section-heading text-center text-white mb-5">
      <span class="eyebrow">pagamento</span>
      <h1 class="display-5">Pagamento do pedido</h1>
      <p class="lead mb-0">Revise o pedido e conclua o pagamento com seguranca pelo PayPal.</p>
    </div>

    <div class="payment-shell">
      <?php if ($payment_success !== '') { ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($payment_success); ?></div>
      <?php } ?>

      <?php if ($payment_error !== '') { ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($payment_error); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger mb-0" role="alert">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php } else { ?>
        <div class="row g-4">
          <div class="col-lg-5">
            <article class="payment-card h-100">
              <h2 class="h4 mb-4">Resumo do pedido</h2>
              <dl class="payment-summary-list mb-4">
                <div>
                  <dt>Pedido</dt>
                  <dd>#<?php echo (int) $order['order_id']; ?></dd>
                </div>
                <div>
                  <dt>Status</dt>
                  <dd><?php echo htmlspecialchars(payment_status_label($order['order_status'])); ?></dd>
                </div>
                <div>
                  <dt>Total</dt>
                  <dd><?php echo htmlspecialchars(store_currency($order['order_cost'])); ?></dd>
                </div>
                <div>
                  <dt>Entrega</dt>
                  <dd><?php echo htmlspecialchars($order['shipping_address'] . ' - ' . $order['shipping_city'] . '/' . $order['shipping_uf']); ?></dd>
                </div>
                <div>
                  <dt>Data do pedido</dt>
                  <dd><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['order_date']))); ?></dd>
                </div>
              </dl>

              <div class="d-grid gap-3">
                <a href="account.php?user_id=<?php echo $session_user_id; ?>" class="btn btn-outline-dark rounded-pill px-4">Voltar para minha conta</a>
                <a href="order_details.php?order_id=<?php echo (int) $order['order_id']; ?>" class="btn btn-link text-start p-0">Ver detalhes do pedido</a>
              </div>
            </article>
          </div>

          <div class="col-lg-7">
            <article class="payment-card h-100">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                  <h2 class="h4 mb-1">Pague com PayPal</h2>
                  <p class="text-muted mb-0">Total a pagar: <?php echo htmlspecialchars(store_currency($order['order_cost'])); ?></p>
                </div>
                <span class="order-status-badge order-status-<?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars(payment_status_label($order['order_status'])); ?></span>
              </div>

              <?php if ($warning_message !== '') { ?>
                <div class="alert alert-warning mb-0" role="alert"><?php echo htmlspecialchars($warning_message); ?></div>
              <?php } else { ?>
                <div class="paypal-card">
                  <div id="paypal-button-container"></div>
                </div>
              <?php } ?>
            </article>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php if ($error_message === '' && $warning_message === '') { ?>
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo rawurlencode($paypal_client_id); ?>&currency=<?php echo rawurlencode($paypal_currency); ?>"></script>
  <script>
    paypal.Buttons({
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: <?php echo json_encode($amount); ?>
            }
          }]
        });
      },
      onApprove: function(data, actions) {
        return actions.order.capture().then(function(orderData) {
          var transaction = orderData.purchase_units[0].payments.captures[0];
          window.location.href =
            'server/complete_payment.php?transaction_id=' + encodeURIComponent(transaction.id) +
            '&order_id=' + encodeURIComponent(<?php echo json_encode((int) $order['order_id']); ?>);
        });
      },
      onError: function() {
        window.location.href = 'order_details.php?order_id=<?php echo (int) $order['order_id']; ?>&payment_error=1';
      }
    }).render('#paypal-button-container');
  </script>
<?php } ?>

<?php include('layouts/footer.php'); ?>