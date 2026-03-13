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
  $product_query = mysqli_prepare($conn, 'SELECT product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id = ? LIMIT 1');

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

  $images = array(
    'product_image' => $product['product_image'],
    'product_image2' => $product['product_image2'],
    'product_image3' => $product['product_image3'],
    'product_image4' => $product['product_image4'],
  );

  foreach (array_keys($images) as $field_name) {
    if (isset($_FILES[$field_name]) && ($_FILES[$field_name]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
      $uploaded_file = admin_upload_image($_FILES[$field_name], $error_message);

      if ($uploaded_file === null) {
        break;
      }

      $images[$field_name] = $uploaded_file;
    }
  }

  if ($error_message === '') {
    $update_images = mysqli_prepare($conn, 'UPDATE products SET product_image = ?, product_image2 = ?, product_image3 = ?, product_image4 = ? WHERE product_id = ?');

    if ($update_images) {
      mysqli_stmt_bind_param($update_images, 'ssssi', $images['product_image'], $images['product_image2'], $images['product_image3'], $images['product_image4'], $product_id);
      mysqli_stmt_execute($update_images);
      mysqli_stmt_close($update_images);
      admin_set_flash('success', 'Imagens do produto atualizadas com sucesso.');
      admin_redirect('products.php');
    }

    $error_message = 'Não foi possível atualizar as imagens do produto.';
  }
}

$product_query = mysqli_prepare($conn, 'SELECT product_id, product_name, product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id = ? LIMIT 1');

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
          <h1 class="h2 mb-1">Editar Imagens</h1>
          <p class="text-muted mb-0">Atualize até 4 imagens do produto.</p>
        </div>
        <a href="products.php" class="btn btn-outline-secondary">Voltar</a>
      </div>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body p-4">
          <h2 class="h5 mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h2>
          <form method="POST" action="edit_images.php?product_id=<?php echo (int) $product['product_id']; ?>" enctype="multipart/form-data" class="row g-4">
            <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
            <?php foreach (array('product_image', 'product_image2', 'product_image3', 'product_image4') as $index => $field_name) { ?>
              <div class="col-md-6">
                <label for="<?php echo $field_name; ?>" class="form-label">Imagem <?php echo $index + 1; ?></label>
                <div class="mb-3">
                  <img src="../assets/imgs/<?php echo htmlspecialchars($product[$field_name]); ?>" alt="Preview <?php echo $index + 1; ?>" class="product-gallery-preview">
                </div>
                <input type="file" class="form-control" id="<?php echo $field_name; ?>" name="<?php echo $field_name; ?>" accept=".jpg,.jpeg,.png,.gif,.svg,.webp">
              </div>
            <?php } ?>
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-dark">Salvar imagens</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>
