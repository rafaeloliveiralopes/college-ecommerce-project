<?php include('header.php'); ?>
<?php
ensure_admin_session();

$product_id = (int) ($_GET['product_id'] ?? $_POST['product_id'] ?? 0);
$error_message = '';
$product = null;

if ($product_id <= 0) {
  admin_set_flash('danger', 'Produto inválido.');
  admin_redirect('products.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_name = trim($_POST['product_name'] ?? '');
  $product_category = trim($_POST['product_category'] ?? '');
  $product_description = trim($_POST['product_description'] ?? '');
  $product_price = (float) ($_POST['product_price'] ?? 0);
  $product_special_offer = (int) ($_POST['product_special_offer'] ?? 0);
  $product_color = trim($_POST['product_color'] ?? '');

  if ($product_name === '' || $product_category === '' || $product_description === '' || $product_color === '') {
    $error_message = 'Preencha todos os campos obrigatórios.';
  } elseif ($product_price <= 0) {
    $error_message = 'Informe um preço válido.';
  } elseif ($product_special_offer < 0 || $product_special_offer > 99) {
    $error_message = 'A oferta deve ficar entre 0 e 99.';
  } else {
    $update_product = mysqli_prepare($conn, 'UPDATE products SET product_name = ?, product_category = ?, product_description = ?, product_price = ?, product_special_offer = ?, product_color = ? WHERE product_id = ?');

    if ($update_product) {
      mysqli_stmt_bind_param($update_product, 'sssdisi', $product_name, $product_category, $product_description, $product_price, $product_special_offer, $product_color, $product_id);
      mysqli_stmt_execute($update_product);
      mysqli_stmt_close($update_product);
      admin_set_flash('success', 'Produto atualizado com sucesso.');
      admin_redirect('products.php');
    } else {
      $error_message = 'Não foi possível atualizar o produto.';
    }
  }
}

$product_query = mysqli_prepare($conn, 'SELECT product_id, product_name, product_category, product_description, product_price, product_special_offer, product_color FROM products WHERE product_id = ? LIMIT 1');

if ($product_query) {
  mysqli_stmt_bind_param($product_query, 'i', $product_id);
  mysqli_stmt_execute($product_query);
  $result = mysqli_stmt_get_result($product_query);
  $product = mysqli_fetch_assoc($result);
  mysqli_stmt_close($product_query);
}

if (!$product) {
  admin_set_flash('danger', 'Produto não encontrado.');
  admin_redirect('products.php');
}
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Editar Produto</h1>
          <p class="text-muted mb-0">Edite nome, descrição, preço, oferta, cor e categoria.</p>
        </div>
        <a href="products.php" class="btn btn-outline-secondary">Voltar</a>
      </div>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body p-4">
          <form method="POST" action="edit_product.php?product_id=<?php echo (int) $product['product_id']; ?>" class="row g-3">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
            <div class="col-md-6">
              <label for="product_name" class="form-label">Nome do produto</label>
              <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="product_category" class="form-label">Categoria</label>
              <input type="text" class="form-control" id="product_category" name="product_category" value="<?php echo htmlspecialchars($product['product_category']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="product_price" class="form-label">Preço</label>
              <input type="number" min="0" step="0.01" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars((string) $product['product_price']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="product_special_offer" class="form-label">Oferta (%)</label>
              <input type="number" min="0" max="99" class="form-control" id="product_special_offer" name="product_special_offer" value="<?php echo (int) $product['product_special_offer']; ?>" required>
            </div>
            <div class="col-md-6">
              <label for="product_color" class="form-label">Cor</label>
              <input type="text" class="form-control" id="product_color" name="product_color" value="<?php echo htmlspecialchars($product['product_color']); ?>" required>
            </div>
            <div class="col-12">
              <label for="product_description" class="form-label">Descrição</label>
              <textarea class="form-control" id="product_description" name="product_description" rows="4" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-dark">Salvar alterações</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>

</html>
