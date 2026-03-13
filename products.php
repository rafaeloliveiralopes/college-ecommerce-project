<?php
include('server/connection.php');

$page = max(1, (int) ($_GET['page'] ?? 1));
$items_per_page = 8;
$total_items = 0;
$total_pages = 1;
$products = array();

$count_result = mysqli_query($conn, 'SELECT COUNT(*) AS total_items FROM products');

if ($count_result) {
  $count_row = mysqli_fetch_assoc($count_result);
  $total_items = (int) ($count_row['total_items'] ?? 0);
  $total_pages = max(1, (int) ceil($total_items / $items_per_page));
  $page = min($page, $total_pages);
}

$offset = ($page - 1) * $items_per_page;
$products_query = mysqli_prepare($conn, 'SELECT product_id, product_name, product_price, product_category, product_image FROM products ORDER BY product_id ASC LIMIT ? OFFSET ?');

if ($products_query) {
  mysqli_stmt_bind_param($products_query, 'ii', $items_per_page, $offset);
  mysqli_stmt_execute($products_query);
  $products_result = mysqli_stmt_get_result($products_query);

  while ($row = mysqli_fetch_assoc($products_result)) {
    $products[] = $row;
  }

  mysqli_stmt_close($products_query);
}

include('layouts/header.php');
?>

<section class="catalog-section py-5">
  <div class="container">
    <div class="section-heading text-center text-white mb-5">
      <span class="eyebrow">catalogo</span>
      <h1 class="display-5">Produtos em destaque</h1>
      <p class="lead mb-0">Explore a nossa vitrine e clique em um item para visualizar todos os detalhes.</p>
    </div>

    <div class="row g-4">
      <?php if (count($products) === 0) { ?>
        <div class="col-12">
          <div class="empty-state-card text-center">
            <h2 class="h4 mb-3">Nenhum produto encontrado</h2>
            <p class="mb-0">Cadastre produtos no CMS para que a listagem pública apareça nesta página.</p>
          </div>
        </div>
      <?php } ?>

      <?php foreach ($products as $product) { ?>
        <div class="col-sm-6 col-lg-4 col-xl-3">
          <article class="product-card h-100">
            <a class="product-card__media" href="single_product.php?product_id=<?php echo (int) $product['product_id']; ?>" aria-label="Ver detalhes de <?php echo htmlspecialchars($product['product_name']); ?>">
              <img src="<?php echo htmlspecialchars(store_image_url($product['product_image'], $product['product_name'])); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-card__image">
            </a>
            <div class="product-card__body">
              <span class="product-card__category"><?php echo htmlspecialchars($product['product_category']); ?></span>
              <h2 class="product-card__title">
                <a href="single_product.php?product_id=<?php echo (int) $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a>
              </h2>
              <p class="product-card__price"><?php echo htmlspecialchars(store_currency($product['product_price'])); ?></p>
              <a href="single_product.php?product_id=<?php echo (int) $product['product_id']; ?>" class="btn btn-dark rounded-pill px-4">Ver detalhes</a>
            </div>
          </article>
        </div>
      <?php } ?>
    </div>

    <?php if ($total_pages > 1) { ?>
      <nav class="d-flex justify-content-center mt-5" aria-label="Paginação dos produtos">
        <ul class="pagination storefront-pagination">
          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="products.php?page=<?php echo max(1, $page - 1); ?>">Anterior</a>
          </li>

          <?php for ($current_page = 1; $current_page <= $total_pages; $current_page++) { ?>
            <li class="page-item <?php echo $current_page === $page ? 'active' : ''; ?>">
              <a class="page-link" href="products.php?page=<?php echo $current_page; ?>"><?php echo $current_page; ?></a>
            </li>
          <?php } ?>

          <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="products.php?page=<?php echo min($total_pages, $page + 1); ?>">Próxima</a>
          </li>
        </ul>
      </nav>
    <?php } ?>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
