<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse show">
  <div class="position-sticky pt-4 px-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" aria-current="page" href="index.php">
          <i class="fas fa-home me-2"></i>
          Painel
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
          <i class="fas fa-file-alt me-2"></i>
          Pedidos
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' || basename($_SERVER['PHP_SELF']) === 'edit_product.php' || basename($_SERVER['PHP_SELF']) === 'edit_images.php' ? 'active' : ''; ?>" href="products.php">
          <i class="fas fa-shopping-cart me-2"></i>
          Produtos
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'account.php' ? 'active' : ''; ?>" href="account.php">
          <i class="fas fa-user-circle me-2"></i>
          Contas
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'add_product.php' || basename($_SERVER['PHP_SELF']) === 'create_product.php' ? 'active' : ''; ?>" href="add_product.php">
          <i class="fas fa-plus-circle me-2"></i>
          Adicionar Produto
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="fas fa-sign-out-alt me-2"></i>
          Sair
        </a>
      </li>
    </ul>
  </div>
</nav>
