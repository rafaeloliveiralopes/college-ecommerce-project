<?php include('header.php'); ?>
<?php
ensure_admin_session();

$flash = admin_get_flash();
$page = max(1, (int) ($_GET['page'] ?? 1));
$items_per_page = 5;
list($total_products, $total_pages, $page, $offset) = admin_paginate($conn, 'products', $page, $items_per_page);

$products = array();
$products_query = mysqli_prepare($conn, 'SELECT product_id, product_name, product_image, product_price, product_special_offer, product_category, product_color FROM products ORDER BY product_id ASC LIMIT ? OFFSET ?');

if ($products_query) {
  mysqli_stmt_bind_param($products_query, 'ii', $items_per_page, $offset);
  mysqli_stmt_execute($products_query);
  $result = mysqli_stmt_get_result($products_query);

  while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
  }

  mysqli_stmt_close($products_query);
}
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between flex-wrap align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Produtos</h1>
          <p class="text-muted mb-0">Lista paginada de produtos com 5 itens por página.</p>
        </div>
        <a href="add_product.php" class="btn btn-dark">Novo produto</a>
      </div>

      <?php if ($flash) { ?>
        <div class="alert <?php echo admin_alert_class($flash['type']); ?>">
          <?php echo htmlspecialchars($flash['message']); ?>
        </div>
      <?php } ?>

      <div class="card content-card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>ID do Produto</th>
                  <th>Imagem</th>
                  <th>Nome do Produto</th>
                  <th>Preço</th>
                  <th>Oferta</th>
                  <th>Categoria</th>
                  <th>Cor</th>
                  <th>Editar Imagens</th>
                  <th>Editar</th>
                  <th>Excluir</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($products) === 0) { ?>
                  <tr>
                    <td colspan="10" class="text-center py-4">Nenhum produto cadastrado no banco de dados.</td>
                  </tr>
                <?php } ?>

                <?php foreach ($products as $product) { ?>
                  <tr>
                    <td><?php echo (int) $product['product_id']; ?></td>
                    <td>
                      <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-thumb">
                    </td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars(admin_format_currency($product['product_price'])); ?></td>
                    <td><?php echo (int) $product['product_special_offer']; ?>%</td>
                    <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                    <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                    <td><a href="edit_images.php?product_id=<?php echo (int) $product['product_id']; ?>" class="btn btn-sm btn-warning">Editar imagens</a></td>
                    <td><a href="edit_product.php?product_id=<?php echo (int) $product['product_id']; ?>" class="btn btn-sm btn-primary">Editar</a></td>
                    <td>
                      <form method="POST" action="delete_product.php" onsubmit="return confirm('Deseja excluir este produto?');">
                        <input type="hidden" name="product_id" value="<?php echo (int) $product['product_id']; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php admin_render_pagination('products.php', $page, $total_pages); ?>
    </main>
  </div>
</div>
</body>
</html>
