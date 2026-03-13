<?php include('header.php'); ?>
<?php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
  admin_redirect('index.php');
}

$error_message = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_error']);

$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['admin_identifier'] ?? '');
  $password = trim($_POST['admin_password'] ?? '');

  if ($identifier === '' || $password === '') {
    $error_message = 'Preencha usuario e senha para continuar.';
  } else {
    $hashed_password = md5($password);
    $stmt = mysqli_prepare($conn, 'SELECT admin_id, admin_name, admin_email FROM admins WHERE (admin_name = ? OR admin_email = ?) AND admin_password = ? LIMIT 1');

    if ($stmt) {
      mysqli_stmt_bind_param($stmt, 'sss', $identifier, $identifier, $hashed_password);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $admin = mysqli_fetch_assoc($result);
      mysqli_stmt_close($stmt);

      if ($admin) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = (int) $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['admin_name'];
        $_SESSION['admin_email'] = $admin['admin_email'];
        admin_redirect('index.php');
      } else {
        $error_message = 'Usuario ou senha invalidos.';
      }
    } else {
      $error_message = 'Nao foi possivel processar o login.';
    }
  }
}
?>
<main class="container login-wrapper d-flex align-items-center justify-content-center py-5">
  <div class="card content-card w-100" style="max-width: 430px;">
    <div class="card-body p-4 p-md-5">
      <h1 class="h3 mb-3 text-center">Login do Dashboard</h1>
      <p class="text-muted text-center mb-4">Entre com o usuario administrador para acessar os pedidos.</p>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger" role="alert">
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php } ?>

      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="admin_identifier" class="form-label">Usuario</label>
          <input type="text" class="form-control" id="admin_identifier" name="admin_identifier" value="<?php echo htmlspecialchars($identifier); ?>" placeholder="admin ou admin@shop.com.br">
        </div>
        <div class="mb-4">
          <label for="admin_password" class="form-label">Senha</label>
          <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="Digite sua senha">
        </div>
        <button type="submit" class="btn btn-dark w-100">Login</button>
      </form>
    </div>
  </div>
</main>
</body>
</html>
