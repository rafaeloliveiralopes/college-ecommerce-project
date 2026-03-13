<?php include('header.php'); ?>
<?php
ensure_admin_session();

$allowed_status = array(
  'on_hold' => 'Em análise',
  'paid' => 'Pago',
  'shipped' => 'Enviado',
  'delivered' => 'Entregue',
);

$order_id = (int) ($_GET['order_id'] ?? $_POST['order_id'] ?? 0);
$success_message = $_SESSION['admin_success'] ?? '';
$error_message = '';
$order = null;
unset($_SESSION['admin_success']);

if ($order_id <= 0) {
  $_SESSION['admin_error'] = 'Pedido inválido.';
  admin_redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_status'])) {
  $order_status = $_POST['order_status'];

  if (!array_key_exists($order_status, $allowed_status)) {
      $error_message = 'Selecione um status válido.';
  } else {
    $update_stmt = mysqli_prepare($conn, 'UPDATE orders SET order_status = ? WHERE order_id = ?');

    if ($update_stmt) {
      mysqli_stmt_bind_param($update_stmt, 'si', $order_status, $order_id);
      mysqli_stmt_execute($update_stmt);
      mysqli_stmt_close($update_stmt);
      $_SESSION['admin_success'] = 'Status do pedido atualizado com sucesso.';
      admin_redirect('edit_order.php?order_id=' . $order_id);
    } else {
      $error_message = 'Não foi possível atualizar o pedido.';
    }
  }
}

$order_stmt = mysqli_prepare($conn, 'SELECT order_id, order_cost, order_status, user_id, shipping_city, shipping_uf, shipping_address, order_date FROM orders WHERE order_id = ? LIMIT 1');

if ($order_stmt) {
  mysqli_stmt_bind_param($order_stmt, 'i', $order_id);
  mysqli_stmt_execute($order_stmt);
  $result = mysqli_stmt_get_result($order_stmt);
  $order = mysqli_fetch_assoc($result);
  mysqli_stmt_close($order_stmt);
}

if (!$order) {
  $_SESSION['admin_error'] = 'Pedido não encontrado.';
  admin_redirect('index.php');
}
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Editar Pedido #<?php echo (int) $order['order_id']; ?></h1>
          <p class="text-muted mb-0">Edição apenas do status, conforme o roteiro.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary">Voltar</a>
      </div>

      <?php if ($success_message !== '') { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body p-4">
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <label class="form-label text-muted">Cliente</label>
              <input type="text" class="form-control" value="Usuario <?php echo (int) $order['user_id']; ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted">Valor</label>
              <input type="text" class="form-control" value="R$ <?php echo number_format((float) $order['order_cost'], 2, ',', '.'); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted">Entrega</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['shipping_city'] . '/' . $order['shipping_uf']); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label text-muted">Data</label>
              <input type="text" class="form-control" value="<?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($order['order_date']))); ?>" disabled>
            </div>
          </div>

          <form method="POST" action="edit_order.php?order_id=<?php echo (int) $order['order_id']; ?>">
            <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
            <div class="mb-4">
              <label for="order_status" class="form-label">Status do pedido</label>
              <select class="form-select" id="order_status" name="order_status">
                <?php foreach ($allowed_status as $value => $label) { ?>
                  <option value="<?php echo $value; ?>" <?php echo $order['order_status'] === $value ? 'selected' : ''; ?>>
                    <?php echo $label; ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <button type="submit" class="btn btn-dark">Salvar alterações</button>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
