<?php include('header.php'); ?>
<?php
ensure_admin_session();

$success_message = $_SESSION['admin_success'] ?? '';
$error_message = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);

$page = max(1, (int) ($_GET['page'] ?? 1));
$items_per_page = 5;
$offset = ($page - 1) * $items_per_page;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
  $delete_order_id = (int) ($_POST['delete_order_id'] ?? 0);

  if ($delete_order_id > 0) {
    mysqli_begin_transaction($conn);

    try {
      $delete_payments = mysqli_prepare($conn, 'DELETE FROM payments WHERE order_id = ?');
      $delete_items = mysqli_prepare($conn, 'DELETE FROM order_items WHERE order_id = ?');
      $delete_order = mysqli_prepare($conn, 'DELETE FROM orders WHERE order_id = ?');

      if (!$delete_payments || !$delete_items || !$delete_order) {
        throw new Exception('Falha ao preparar a exclusao.');
      }

      mysqli_stmt_bind_param($delete_payments, 'i', $delete_order_id);
      mysqli_stmt_execute($delete_payments);
      mysqli_stmt_close($delete_payments);

      mysqli_stmt_bind_param($delete_items, 'i', $delete_order_id);
      mysqli_stmt_execute($delete_items);
      mysqli_stmt_close($delete_items);

      mysqli_stmt_bind_param($delete_order, 'i', $delete_order_id);
      mysqli_stmt_execute($delete_order);

      if (mysqli_stmt_affected_rows($delete_order) > 0) {
        $_SESSION['admin_success'] = 'Pedido excluido com sucesso.';
      } else {
        $_SESSION['admin_error'] = 'Pedido nao encontrado para exclusao.';
      }

      mysqli_stmt_close($delete_order);
      mysqli_commit($conn);
      admin_redirect('index.php?page=' . $page);
    } catch (Throwable $exception) {
      mysqli_rollback($conn);
      $error_message = 'Nao foi possivel excluir o pedido.';
    }
  }
}

$total_orders_result = mysqli_query($conn, 'SELECT COUNT(*) AS total_orders FROM orders');
$total_orders_row = $total_orders_result ? mysqli_fetch_assoc($total_orders_result) : array('total_orders' => 0);
$total_orders = (int) ($total_orders_row['total_orders'] ?? 0);
$total_pages = max(1, (int) ceil($total_orders / $items_per_page));

if ($page > $total_pages) {
  $page = $total_pages;
  $offset = ($page - 1) * $items_per_page;
}

$orders = array();
$orders_query = mysqli_prepare($conn, 'SELECT order_id, order_cost, order_status, user_id, shipping_city, shipping_uf, shipping_address, order_date FROM orders ORDER BY order_date DESC, order_id DESC LIMIT ? OFFSET ?');

if ($orders_query) {
  mysqli_stmt_bind_param($orders_query, 'ii', $items_per_page, $offset);
  mysqli_stmt_execute($orders_query);
  $result = mysqli_stmt_get_result($orders_query);

  while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
  }

  mysqli_stmt_close($orders_query);
}
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Pedidos</h1>
          <p class="text-muted mb-0">Lista paginada com 5 pedidos por pagina.</p>
        </div>
      </div>

      <?php if ($success_message !== '') { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Cliente</th>
                  <th>Status</th>
                  <th>Valor</th>
                  <th>Entrega</th>
                  <th>Data</th>
                  <th class="text-end">Acoes</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($orders) === 0) { ?>
                  <tr>
                    <td colspan="7" class="text-center py-4">Nenhum pedido encontrado no banco de dados.</td>
                  </tr>
                <?php } ?>

                <?php foreach ($orders as $order) { ?>
                  <tr>
                    <td>#<?php echo (int) $order['order_id']; ?></td>
                    <td>Usuario <?php echo (int) $order['user_id']; ?></td>
                    <td><?php echo htmlspecialchars(admin_status_label($order['order_status'])); ?></td>
                    <td>R$ <?php echo number_format((float) $order['order_cost'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($order['shipping_city'] . '/' . $order['shipping_uf']); ?><br><small class="text-muted"><?php echo htmlspecialchars($order['shipping_address']); ?></small></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($order['order_date']))); ?></td>
                    <td class="text-end">
                      <a href="edit_order.php?order_id=<?php echo (int) $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                      <form method="POST" action="index.php?page=<?php echo (int) $page; ?>" class="d-inline">
                        <input type="hidden" name="delete_order_id" value="<?php echo (int) $order['order_id']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este pedido?');">Excluir</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <nav class="mt-4" aria-label="Paginacao de pedidos">
        <ul class="pagination">
          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?page=<?php echo max(1, $page - 1); ?>">Anterior</a>
          </li>

          <?php for ($current_page = 1; $current_page <= $total_pages; $current_page++) { ?>
            <li class="page-item <?php echo $current_page === $page ? 'active' : ''; ?>">
              <a class="page-link" href="index.php?page=<?php echo $current_page; ?>"><?php echo $current_page; ?></a>
            </li>
          <?php } ?>

          <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?page=<?php echo min($total_pages, $page + 1); ?>">Proxima</a>
          </li>
        </ul>
      </nav>
    </main>
  </div>
</div>
</body>
</html>
