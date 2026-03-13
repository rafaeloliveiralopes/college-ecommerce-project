<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function checkout_cart_summary()
{
  $total_price = 0.0;
  $total_quantity = 0;

  if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['total'] = 0;
    $_SESSION['quantity'] = 0;

    return array('total' => 0.0, 'quantity' => 0);
  }

  foreach ($_SESSION['cart'] as $item) {
    if (!is_array($item)) {
      continue;
    }

    $quantity = max(0, (int) ($item['product_quantity'] ?? 0));
    $price = (float) ($item['product_price'] ?? 0);

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

$cart_summary = checkout_cart_summary();

if ($cart_summary['quantity'] === 0) {
  header('Location: index.php');
  exit;
}

$success_message = (string) ($_SESSION['checkout_success'] ?? '');
$error_message = (string) ($_SESSION['checkout_error'] ?? '');
$form_data = $_SESSION['checkout_form'] ?? array();
unset($_SESSION['checkout_success'], $_SESSION['checkout_error']);

include('layouts/header.php');
?>

<section class="checkout-section py-5">
  <div class="container">
    <div class="section-heading text-center text-white mb-5">
      <span class="eyebrow">finalizacao</span>
      <h1 class="display-5">Finalizar pedido</h1>
      <p class="lead mb-0">Informe os dados de entrega e conclua o seu pedido com seguranca.</p>
    </div>

    <div class="checkout-shell">
      <div class="row g-4">
        <div class="col-lg-7">
          <article class="checkout-card h-100">
            <h2 class="h4 mb-4">Endereco de entrega</h2>

            <?php if ($success_message !== '') { ?>
              <div class="alert alert-success" role="alert"><?php echo $success_message; ?></div>
            <?php } ?>

            <?php if ($error_message !== '') { ?>
              <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
            <?php } ?>

            <form action="server/place_order.php" method="POST" class="row g-3">
              <div class="col-md-4">
                <label for="shipping_uf" class="form-label">UF</label>
                <input
                  type="text"
                  class="form-control text-uppercase"
                  id="shipping_uf"
                  name="shipping_uf"
                  maxlength="2"
                  value="<?php echo htmlspecialchars((string) ($form_data['shipping_uf'] ?? '')); ?>"
                  placeholder="SP"
                >
              </div>

              <div class="col-md-8">
                <label for="shipping_city" class="form-label">Cidade</label>
                <input
                  type="text"
                  class="form-control"
                  id="shipping_city"
                  name="shipping_city"
                  value="<?php echo htmlspecialchars((string) ($form_data['shipping_city'] ?? '')); ?>"
                  placeholder="Digite a cidade"
                >
              </div>

              <div class="col-12">
                <label for="shipping_address" class="form-label">Endereco</label>
                <input
                  type="text"
                  class="form-control"
                  id="shipping_address"
                  name="shipping_address"
                  value="<?php echo htmlspecialchars((string) ($form_data['shipping_address'] ?? '')); ?>"
                  placeholder="Rua, numero e complemento"
                >
              </div>

              <div class="col-12 d-grid d-sm-flex justify-content-sm-end">
                <button type="submit" class="btn btn-dark rounded-pill px-4">Concluir pedido</button>
              </div>
            </form>
          </article>
        </div>

        <div class="col-lg-5">
          <article class="checkout-card h-100">
            <h2 class="h4 mb-4">Resumo do pedido</h2>
            <dl class="cart-summary-list mb-4">
              <div>
                <dt>Total de itens</dt>
                <dd><?php echo (int) $cart_summary['quantity']; ?></dd>
              </div>
              <div>
                <dt>Total do pedido</dt>
                <dd><?php echo htmlspecialchars(store_currency($cart_summary['total'])); ?></dd>
              </div>
            </dl>
            <p class="checkout-card__note mb-4">O pedido sera validado no servidor e somente usuarios logados poderao concluir a gravacao no banco.</p>
            <div class="d-flex flex-column gap-3">
              <a href="cart.php" class="btn btn-outline-dark rounded-pill px-4">Voltar ao carrinho</a>
              <?php if (!$is_user_logged_in) { ?>
                <a href="login.php" class="btn btn-link p-0 text-start">Ja possui conta? Entre para concluir mais rapido.</a>
              <?php } ?>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
