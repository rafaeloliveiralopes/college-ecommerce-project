<?php
include('server/connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
  header('Location: account.php?user_id=' . (int) $_SESSION['user_id']);
  exit;
}

$name = trim((string) ($_POST['user_name'] ?? ''));
$email = trim((string) ($_POST['user_email'] ?? ''));
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = trim((string) ($_POST['user_password'] ?? ''));
  $confirm_password = trim((string) ($_POST['confirm_password'] ?? ''));

  if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
    $error_message = 'Preencha todos os campos do cadastro.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_message = 'Informe um e-mail valido.';
  } elseif (strlen($password) < 6) {
    $error_message = 'A senha deve ter pelo menos 6 caracteres.';
  } elseif ($password !== $confirm_password) {
    $error_message = 'A confirmacao da senha nao confere.';
  } else {
    $email_check_query = mysqli_prepare($conn, 'SELECT user_id FROM users WHERE user_email = ? LIMIT 1');

    if ($email_check_query) {
      mysqli_stmt_bind_param($email_check_query, 's', $email);
      mysqli_stmt_execute($email_check_query);
      $email_exists = mysqli_fetch_assoc(mysqli_stmt_get_result($email_check_query)) ?: null;
      mysqli_stmt_close($email_check_query);

      if ($email_exists) {
        $error_message = 'Ja existe um usuario cadastrado com esse e-mail.';
      } else {
        $insert_user_query = mysqli_prepare($conn, 'INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)');

        if ($insert_user_query) {
          $hashed_password = md5($password);
          mysqli_stmt_bind_param($insert_user_query, 'sss', $name, $email, $hashed_password);

          if (mysqli_stmt_execute($insert_user_query)) {
            mysqli_stmt_close($insert_user_query);
            header('Location: login.php?registered=1');
            exit;
          }

          mysqli_stmt_close($insert_user_query);
        }

        $error_message = 'Nao foi possivel concluir o cadastro.';
      }
    } else {
      $error_message = 'Nao foi possivel validar o e-mail informado.';
    }
  }
}

include('layouts/header.php');
?>

<section class="auth-section py-5">
  <div class="container">
    <div class="auth-card mx-auto">
      <div class="auth-card__content">
        <span class="eyebrow">novo cadastro</span>
        <h1 class="mb-3">Criar conta</h1>
        <p class="auth-card__subtitle">Preencha seus dados para acompanhar pedidos e acessar sua area do cliente.</p>

        <?php if ($error_message !== '') { ?>
          <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php } ?>

        <form method="POST" action="register.php" class="row g-3">
          <div class="col-12">
            <label for="user_name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Digite seu nome completo">
          </div>

          <div class="col-12">
            <label for="user_email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo htmlspecialchars($email); ?>" placeholder="voce@exemplo.com">
          </div>

          <div class="col-md-6">
            <label for="user_password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Minimo de 6 caracteres">
          </div>

          <div class="col-md-6">
            <label for="confirm_password" class="form-label">Confirmar senha</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repita sua senha">
          </div>

          <div class="col-12 d-grid">
            <button type="submit" class="btn btn-dark rounded-pill py-2">Cadastrar-se</button>
          </div>
        </form>

        <p class="auth-card__link mb-0">Ja possui conta? <a href="login.php">Faca login</a></p>
      </div>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
