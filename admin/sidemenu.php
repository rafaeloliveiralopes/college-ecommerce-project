<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse show">
  <div class="position-sticky pt-4 px-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" aria-current="page" href="index.php">
          <i class="fas fa-home me-2"></i>
          Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
          <i class="fas fa-file-alt me-2"></i>
          Orders
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-shopping-cart me-2"></i>
          Products
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-user-circle me-2"></i>
          Account
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-plus-circle me-2"></i>
          Add New Product
        </a>
      </li>
    </ul>
  </div>
</nav>
