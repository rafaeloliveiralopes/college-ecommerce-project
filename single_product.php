<?php
include('server/connection.php');

$product_id = (int) ($_GET['product_id'] ?? 0);
$product = null;

if ($product_id > 0) {
  $product_query = mysqli_prepare($conn, 'SELECT product_id, product_name, product_category, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_special_offer, product_color FROM products WHERE product_id = ? LIMIT 1');

  if ($product_query) {
    mysqli_stmt_bind_param($product_query, 'i', $product_id);
    mysqli_stmt_execute($product_query);
    $product_result = mysqli_stmt_get_result($product_query);
    $product = mysqli_fetch_assoc($product_result) ?: null;
    mysqli_stmt_close($product_query);
  }
}

include('layouts/header.php');

$gallery_images = array();

if ($product) {
  $image_fields = array('product_image', 'product_image2', 'product_image3', 'product_image4');

  foreach ($image_fields as $field) {
    $image_url = store_image_url($product[$field] ?? '', $product['product_name']);

    if (!in_array($image_url, $gallery_images, true)) {
      $gallery_images[] = $image_url;
    }
  }
}
?>

<section class="product-detail-section py-5">
  <div class="container">
    <?php if (!$product) { ?>
      <div class="empty-state-card text-center">
        <h1 class="h3 mb-3">Produto não encontrado</h1>
        <p class="mb-4">O item solicitado não está disponível ou o identificador informado é inválido.</p>
        <a href="products.php" class="btn btn-dark rounded-pill px-4">Voltar para produtos</a>
      </div>
    <?php } else { ?>
      <div class="detail-shell">
        <div class="row g-4 align-items-start">
          <div class="col-lg-6">
            <div class="product-viewer">
              <div class="product-viewer__hero">
                <img
                  src="<?php echo htmlspecialchars($gallery_images[0]); ?>"
                  alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                  id="mainProductImage"
                  class="product-viewer__main-image"
                >
              </div>
              <div class="row g-3 mt-1">
                <?php foreach ($gallery_images as $index => $image_url) { ?>
                  <div class="col-3">
                    <button
                      type="button"
                      class="product-thumb-button <?php echo $index === 0 ? 'is-active' : ''; ?>"
                      data-image="<?php echo htmlspecialchars($image_url); ?>"
                      data-alt="<?php echo htmlspecialchars($product['product_name'] . ' - imagem ' . ($index + 1)); ?>"
                      aria-label="Visualizar imagem <?php echo $index + 1; ?> de <?php echo htmlspecialchars($product['product_name']); ?>"
                    >
                      <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($product['product_name'] . ' - miniatura ' . ($index + 1)); ?>" class="product-thumb-button__image">
                    </button>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="product-detail-card">
              <a href="products.php" class="detail-back-link">Voltar para a listagem</a>
              <span class="detail-category"><?php echo htmlspecialchars($product['product_category']); ?></span>
              <h1 class="detail-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
              <p class="detail-price"><?php echo htmlspecialchars(store_currency($product['product_price'])); ?></p>
              <ul class="detail-meta list-unstyled">
                <li><strong>Cor:</strong> <?php echo htmlspecialchars($product['product_color']); ?></li>
                <li><strong>Oferta:</strong> <?php echo (int) $product['product_special_offer']; ?>%</li>
              </ul>
              <p class="detail-description"><?php echo htmlspecialchars($product['product_description']); ?></p>

              <form class="detail-form" action="#" method="post">
                <label for="quantity" class="form-label">Quantidade</label>
                <div class="detail-form__row">
                  <input type="number" min="1" value="1" id="quantity" name="quantity" class="form-control">
                  <button type="button" class="btn btn-dark rounded-pill px-4">Adicionar ao carrinho</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</section>

<?php if ($product) { ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var mainImage = document.getElementById('mainProductImage');
      var thumbnailButtons = document.querySelectorAll('.product-thumb-button');

      thumbnailButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          if (!mainImage) {
            return;
          }

          mainImage.src = button.getAttribute('data-image');
          mainImage.alt = button.getAttribute('data-alt');

          thumbnailButtons.forEach(function (item) {
            item.classList.remove('is-active');
          });

          button.classList.add('is-active');
        });
      });
    });
  </script>
<?php } ?>

<?php include('layouts/footer.php'); ?>
