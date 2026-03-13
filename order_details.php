<?php
include('server/connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function order_details_status_label($status)
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
$order_id = (int) ($_POST['order_id'] ?? $_GET['order_id'] ?? 0);
$success_message = (string) ($_SESSION['order_details_success'] ?? '');
$error_message = (string) ($_SESSION['order_details_error'] ?? '');
unset($_SESSION['order_details_success'], $_SESSION['order_details_error']);

if (isset($_GET['payment_error']) && $_GET['payment_error'] === '1' && $error_message === '') {
  $error_message = 'Nao foi possivel concluir o pagamento com PayPal. Tente novamente.';
}

$order = null;
$order_items = array();

if ($order_id <= 0) {
  $error_message = 'Selecione um pedido valido para visualizar os detalhes.';
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
  } else {
    $items_query = mysqli_prepare(
      $conn,
      'SELECT p.product_name, p.product_price, oi.qnt
       FROM order_items oi
       INNER JOIN products p ON p.product_id = oi.product_id
       WHERE oi.order_id = ? AND oi.user_id = ?
       ORDER BY oi.item_id ASC'
    );

    if ($items_query) {
      mysqli_stmt_bind_param($items_query, 'ii', $order_id, $session_user_id);
      mysqli_stmt_execute($items_query);
      $items_result = mysqli_stmt_get_result($items_query);

      while ($row = mysqli_fetch_assoc($items_result)) {
        $order_items[] = $row;
      }

      mysqli_stmt_close($items_query);
    }
  }
}

include('layouts/header.php');
?>

<section class="order-details-section py-5">
  <div class="container">
    <div class="section-heading text-center text-white mb-5">
      <span class="eyebrow">pedido</span>
      <h1 class="display-5">Detalhes do pedido</h1>
      <p class="lead mb-0">Consulte os itens comprados, o status atual e siga para o pagamento quando necessario.</p>
    </div>

    <div class="order-details-shell">
      <?php if ($success_message !== '') { ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger mb-0" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } elseif ($order) { ?>
        <div class="row g-4">
          <div class="col-lg-4">
            <article class="payment-card h-100">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                <div>
                  <h2 class="h4 mb-1">Pedido #<?php echo (int) $order['order_id']; ?></h2>
                  <p class="text-muted mb-0">Criado em <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['order_date']))); ?></p>
                </div>
                <?php $status_class = strtolower(str_replace(' ', '-', (string) $order['order_status'])); ?>
                <span class="order-status-badge order-status-<?php echo htmlspecialchars($status_class); ?>">
                  <?php echo htmlspecialchars(order_details_status_label($order['order_status'])); ?>
                </span>
              </div>

              <dl class="payment-summary-list mb-4">
                <div>
                  <dt>Total</dt>
                  <dd><?php echo htmlspecialchars(store_currency($order['order_cost'])); ?></dd>
                </div>
                <div>
                  <dt>Cidade</dt>
                  <dd><?php echo htmlspecialchars($order['shipping_city']); ?></dd>
                </div>
                <div>
                  <dt>UF</dt>
                  <dd><?php echo htmlspecialchars($order['shipping_uf']); ?></dd>
                </div>
                <div>
                  <dt>Endereco</dt>
                  <dd><?php echo htmlspecialchars($order['shipping_address']); ?></dd>
                </div>
              </dl>

              <div class="d-grid gap-3">
                <a href="account.php?user_id=<?php echo $session_user_id; ?>" class="btn btn-outline-dark rounded-pill px-4">Voltar para minha conta</a>

                <?php if ($order['order_status'] === 'not paid') { ?>
                  <form method="POST" action="payments.php">
                    <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
                    <button type="submit" class="btn btn-dark rounded-pill w-100">Pagar agora</button>
                  </form>
                <?php } ?>
              </div>
            </article>
          </div>

          <div class="col-lg-8">
            <article class="payment-card h-100">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                <div>
                  <h2 class="h4 mb-1">Itens do pedido</h2>
                  <p class="text-muted mb-0">Confira nome do produto, preco e quantidade solicitada.</p>
                </div>
                <span class="account-orders-count"><?php echo count($order_items); ?> item(ns)</span>
              </div>

              <?php if (count($order_items) === 0) { ?>
                <div class="empty-state-card text-center">
                  <h3 class="h5 mb-3">Nenhum item encontrado</h3>
                  <p class="mb-0">Os itens deste pedido nao puderam ser carregados no momento.</p>
                </div>
              <?php } else { ?>
                <div class="table-responsive">
                  <table class="table account-orders-table align-middle mb-0">
                    <thead>
                      <tr>
                        <th>Produto</th>
                        <th>Preco</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($order_items as $item) { ?>
                        <?php $subtotal = (float) $item['product_price'] * (int) $item['qnt']; ?>
                        <tr>
                          <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                          <td><?php echo htmlspecialchars(store_currency($item['product_price'])); ?></td>
                          <td><?php echo (int) $item['qnt']; ?></td>
                          <td><?php echo htmlspecialchars(store_currency($subtotal)); ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              <?php } ?>
            </article>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
