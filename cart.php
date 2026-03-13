<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function recalculate_cart_summary()
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

    $_SESSION['cart'][$product_id]['product_quantity'] = $quantity;
    $total_quantity += $quantity;
    $total_price += $price * $quantity;
  }

  if ($total_quantity === 0) {
    unset($_SESSION['cart']);
  }

  $_SESSION['total'] = $total_price;
  $_SESSION['quantity'] = $total_quantity;

  return array(
    'total' => $total_price,
    'quantity' => $total_quantity,
  );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = (string) ($_POST['action'] ?? '');
  $product_id = (int) ($_POST['product_id'] ?? 0);

  if ($action === 'update_quantity') {
    $quantity = filter_var($_POST['product_quantity'] ?? 0, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

    if ($product_id > 0 && $quantity !== false && isset($_SESSION['cart'][$product_id])) {
      $_SESSION['cart'][$product_id]['product_quantity'] = (int) $quantity;
      recalculate_cart_summary();
      $_SESSION['cart_success'] = 'Quantidade atualizada com sucesso.';
    } else {
      $_SESSION['cart_error'] = 'Nao foi possivel atualizar a quantidade do produto.';
    }

    header('Location: cart.php');
    exit;
  }

  if ($action === 'remove_product') {
    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
      unset($_SESSION['cart'][$product_id]);
      recalculate_cart_summary();
      $_SESSION['cart_success'] = 'Produto removido do carrinho.';
    } else {
      $_SESSION['cart_error'] = 'Nao foi possivel remover o produto selecionado.';
    }

    header('Location: cart.php');
    exit;
  }
}

$cart_summary = recalculate_cart_summary();
$success_message = (string) ($_SESSION['cart_success'] ?? '');
$error_message = (string) ($_SESSION['cart_error'] ?? '');
unset($_SESSION['cart_success'], $_SESSION['cart_error']);

$cart_items = array_values($_SESSION['cart'] ?? array());

include('layouts/header.php');
?>

<section class="cart-section py-5">
  <div class="container">
    <div class="section-heading text-center text-white mb-5">
      <span class="eyebrow">carrinho</span>
      <h1 class="display-5">Seu carrinho</h1>
      <p class="lead mb-0">Revise os itens, ajuste as quantidades e siga para a finalizacao quando estiver tudo certo.</p>
    </div>

    <div class="cart-shell">
      <?php if ($success_message !== '') { ?>
        <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <?php if (count($cart_items) === 0) { ?>
        <div class="empty-state-card text-center">
          <h2 class="h4 mb-3">Seu carrinho esta vazio</h2>
          <p class="mb-4">Adicione produtos para visualizar os itens e prosseguir para a finalizacao da compra.</p>
          <a href="products.php" class="btn btn-dark rounded-pill px-4">Explorar produtos</a>
        </div>
      <?php } else { ?>
        <div class="table-responsive">
          <table class="table cart-table align-middle mb-0">
            <thead>
              <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
                <th>Editar</th>
                <th>Excluir</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart_items as $item) { ?>
                <?php
                $product_id = (int) ($item['product_id'] ?? 0);
                $product_name = (string) ($item['product_name'] ?? '');
                $product_price = (float) ($item['product_price'] ?? 0);
                $product_quantity = (int) ($item['product_quantity'] ?? 0);
                $product_subtotal = $product_price * $product_quantity;
                $update_form_id = 'update-cart-item-' . $product_id;
                ?>
                <tr>
                  <td>
                    <div class="cart-product">
                      <img
                        src="<?php echo htmlspecialchars(store_image_url((string) ($item['product_image'] ?? ''), $product_name)); ?>"
                        alt="<?php echo htmlspecialchars($product_name); ?>"
                        class="cart-product__image"
                      >
                      <div>
                        <strong class="d-block"><?php echo htmlspecialchars($product_name); ?></strong>
                        <span class="text-muted"><?php echo htmlspecialchars(store_currency($product_price)); ?> cada</span>
                      </div>
                    </div>
                  </td>
                  <td class="cart-table__quantity">
                    <form action="cart.php" method="POST" id="<?php echo htmlspecialchars($update_form_id); ?>" class="cart-update-form">
                      <input type="hidden" name="action" value="update_quantity">
                      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                      <input
                        type="number"
                        min="1"
                        name="product_quantity"
                        value="<?php echo $product_quantity; ?>"
                        class="form-control"
                        aria-label="Quantidade de <?php echo htmlspecialchars($product_name); ?>"
                      >
                    </form>
                  </td>
                  <td><?php echo htmlspecialchars(store_currency($product_subtotal)); ?></td>
                  <td>
                    <button type="submit" form="<?php echo htmlspecialchars($update_form_id); ?>" class="btn btn-outline-dark rounded-pill px-3">Atualizar</button>
                  </td>
                  <td>
                    <form action="cart.php" method="POST">
                      <input type="hidden" name="action" value="remove_product">
                      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                      <button type="submit" class="btn btn-outline-danger rounded-pill px-3">Excluir</button>
                    </form>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <div class="cart-summary-card ms-lg-auto mt-4">
          <h2 class="h4 mb-4">Resumo do carrinho</h2>
          <dl class="cart-summary-list mb-4">
            <div>
              <dt>Itens</dt>
              <dd><?php echo (int) $cart_summary['quantity']; ?></dd>
            </div>
            <div>
              <dt>Total</dt>
              <dd><?php echo htmlspecialchars(store_currency($cart_summary['total'])); ?></dd>
            </div>
          </dl>
          <div class="d-flex flex-column flex-sm-row gap-3">
            <a href="products.php" class="btn btn-outline-dark rounded-pill px-4">Continuar comprando</a>
            <a href="checkout.php" class="btn btn-dark rounded-pill px-4">Ir para finalizacao</a>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
