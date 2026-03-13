<?php include('header.php'); ?>
<?php
ensure_admin_session();

$flash = admin_get_flash();
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Adicionar Novo Produto</h1>
          <p class="text-muted mb-0">Cadastre um novo produto no CMS.</p>
        </div>
        <a href="products.php" class="btn btn-outline-secondary">Voltar</a>
      </div>

      <?php if ($flash) { ?>
        <div class="alert <?php echo admin_alert_class($flash['type']); ?>">
          <?php echo htmlspecialchars($flash['message']); ?>
        </div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body p-4">
          <form method="POST" action="create_product.php" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
              <label for="product_name" class="form-label">Nome do produto</label>
              <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="col-md-6">
              <label for="product_category" class="form-label">Categoria</label>
              <input type="text" class="form-control" id="product_category" name="product_category" required>
            </div>
            <div class="col-md-6">
              <label for="product_price" class="form-label">Preço</label>
              <input type="number" min="0" step="0.01" class="form-control" id="product_price" name="product_price" required>
            </div>
            <div class="col-md-6">
              <label for="product_special_offer" class="form-label">Oferta (%)</label>
              <input type="number" min="0" max="99" class="form-control" id="product_special_offer" name="product_special_offer" value="0" required>
            </div>
            <div class="col-md-6">
              <label for="product_color" class="form-label">Cor</label>
              <input type="text" class="form-control" id="product_color" name="product_color" required>
            </div>
            <div class="col-md-6">
              <label for="product_image" class="form-label">Imagem principal</label>
              <input type="file" class="form-control" id="product_image" name="product_image" accept=".jpg,.jpeg,.png,.gif,.svg,.webp" required>
            </div>
            <div class="col-12">
              <label for="product_description" class="form-label">Descrição</label>
              <textarea class="form-control" id="product_description" name="product_description" rows="4" required></textarea>
            </div>
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" class="btn btn-dark">Criar produto</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>

</html>
