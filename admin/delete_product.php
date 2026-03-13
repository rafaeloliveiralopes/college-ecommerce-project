<?php
include('header.php');
ensure_admin_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  admin_redirect('products.php');
}

$product_id = (int) ($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
  admin_set_flash('danger', 'Produto inválido para exclusão.');
  admin_redirect('products.php');
}

mysqli_begin_transaction($conn);

try {
  $delete_order_items = mysqli_prepare($conn, 'DELETE FROM order_items WHERE product_id = ?');
  $delete_product = mysqli_prepare($conn, 'DELETE FROM products WHERE product_id = ?');

  if (!$delete_order_items || !$delete_product) {
    throw new Exception('Falha ao preparar a exclusão do produto.');
  }

  mysqli_stmt_bind_param($delete_order_items, 'i', $product_id);
  mysqli_stmt_execute($delete_order_items);
  mysqli_stmt_close($delete_order_items);

  mysqli_stmt_bind_param($delete_product, 'i', $product_id);
  mysqli_stmt_execute($delete_product);

  if (mysqli_stmt_affected_rows($delete_product) <= 0) {
    throw new Exception('Produto não encontrado para exclusão.');
  }

  mysqli_stmt_close($delete_product);
  mysqli_commit($conn);
  admin_set_flash('success', 'Produto excluído com sucesso.');
} catch (Throwable $exception) {
  mysqli_rollback($conn);
  admin_set_flash('danger', 'Não foi possível excluir o produto.');
}

admin_redirect('products.php');
?>
