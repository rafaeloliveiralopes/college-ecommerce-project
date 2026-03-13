<?php
include('server/connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
  header('Location: account.php?user_id=' . (int) $_SESSION['user_id']);
  exit;
}

$email = trim((string) ($_POST['user_email'] ?? ''));
$error_message = '';
$success_message = '';

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
  $success_message = 'Cadastro realizado com sucesso. Faça login para acessar sua conta.';
} elseif (isset($_GET['logout']) && $_GET['logout'] === '1') {
  $success_message = 'Logout realizado com sucesso.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = trim((string) ($_POST['user_password'] ?? ''));

  if ($email === '' || $password === '') {
    $error_message = 'Preencha e-mail e senha para entrar.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_message = 'Informe um e-mail valido.';
  } else {
    $login_query = mysqli_prepare($conn, 'SELECT user_id, user_name, user_email, user_password FROM users WHERE user_email = ? LIMIT 1');

    if ($login_query) {
      mysqli_stmt_bind_param($login_query, 's', $email);
      mysqli_stmt_execute($login_query);
      $login_result = mysqli_stmt_get_result($login_query);
      $user = mysqli_fetch_assoc($login_result) ?: null;
      mysqli_stmt_close($login_query);

      if ($user && $user['user_password'] === md5($password)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = (int) $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_email'] = $user['user_email'];

        header('Location: account.php?user_id=' . (int) $user['user_id']);
        exit;
      }

      $error_message = 'E-mail ou senha invalidos.';
    } else {
      $error_message = 'Nao foi possivel processar o login agora.';
    }
  }
}

include('layouts/header.php');
?>

<section class="auth-section py-5">
  <div class="container">
    <div class="auth-card mx-auto">
      <div class="auth-card__content">
        <span class="eyebrow">cliente</span>
        <h1 class="mb-3">Entrar na sua conta</h1>
        <p class="auth-card__subtitle">Acesse sua area para acompanhar pedidos e atualizar sua senha.</p>

        <?php if ($error_message !== '') { ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php } ?>

        <?php if ($success_message !== '') { ?>
          <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
        <?php } ?>

        <form method="POST" action="login.php" class="row g-3">
          <div class="col-12">
            <label for="user_email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo htmlspecialchars($email); ?>" placeholder="voce@exemplo.com">
          </div>

          <div class="col-12">
            <label for="user_password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Digite sua senha">
          </div>

          <div class="col-12 d-grid">
            <button type="submit" class="btn btn-dark rounded-pill py-2">Entrar</button>
          </div>
        </form>

        <p class="auth-card__link mb-0">Nao possui conta? <a href="register.php">Cadastre-se aqui.</a></p>
      </div>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
