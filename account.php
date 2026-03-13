<?php
include('server/connection.php');

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: login.php');
  exit;
}

function format_order_status_label($status)
{
  $status_map = array(
    'not paid' => 'Nao pago',
    'on_hold' => 'Em analise',
    'paid' => 'Pago',
    'shipped' => 'Enviado',
    'delivered' => 'Entregue',
  );

  $status = (string) $status;

  return $status_map[$status] ?? $status;
}

$session_user_id = (int) ($_SESSION['user_id'] ?? 0);
$requested_user_id = (int) ($_GET['user_id'] ?? 0);

if ($requested_user_id <= 0 || $requested_user_id !== $session_user_id) {
  header('Location: account.php?user_id=' . $session_user_id);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
  $_SESSION = array();

  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }

  session_destroy();
  session_start();
  header('Location: login.php?logout=1');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
  $current_password = trim((string) ($_POST['current_password'] ?? ''));
  $new_password = trim((string) ($_POST['new_password'] ?? ''));
  $confirm_new_password = trim((string) ($_POST['confirm_new_password'] ?? ''));

  if ($current_password === '' || $new_password === '' || $confirm_new_password === '') {
    $_SESSION['account_error'] = 'Preencha todos os campos para alterar a senha.';
  } elseif (strlen($new_password) < 6) {
    $_SESSION['account_error'] = 'A nova senha deve ter pelo menos 6 caracteres.';
  } elseif ($new_password !== $confirm_new_password) {
    $_SESSION['account_error'] = 'A confirmacao da nova senha nao confere.';
  } else {
    $password_query = mysqli_prepare($conn, 'SELECT user_password FROM users WHERE user_id = ? LIMIT 1');

    if ($password_query) {
      mysqli_stmt_bind_param($password_query, 'i', $session_user_id);
      mysqli_stmt_execute($password_query);
      $password_result = mysqli_stmt_get_result($password_query);
      $password_row = mysqli_fetch_assoc($password_result) ?: null;
      mysqli_stmt_close($password_query);

      if (!$password_row || $password_row['user_password'] !== md5($current_password)) {
        $_SESSION['account_error'] = 'A senha atual informada esta incorreta.';
      } elseif ($current_password === $new_password) {
        $_SESSION['account_error'] = 'A nova senha precisa ser diferente da senha atual.';
      } else {
        $update_password_query = mysqli_prepare($conn, 'UPDATE users SET user_password = ? WHERE user_id = ?');

        if ($update_password_query) {
          $hashed_new_password = md5($new_password);
          mysqli_stmt_bind_param($update_password_query, 'si', $hashed_new_password, $session_user_id);

          if (mysqli_stmt_execute($update_password_query)) {
            $_SESSION['account_success'] = 'Senha atualizada com sucesso.';
          } else {
            $_SESSION['account_error'] = 'Nao foi possivel atualizar a senha.';
          }

          mysqli_stmt_close($update_password_query);
        } else {
          $_SESSION['account_error'] = 'Nao foi possivel preparar a alteracao da senha.';
        }
      }
    } else {
      $_SESSION['account_error'] = 'Nao foi possivel validar a senha atual.';
    }
  }

  header('Location: account.php?user_id=' . $session_user_id);
  exit;
}

$success_message = $_SESSION['account_success'] ?? '';
$error_message = $_SESSION['account_error'] ?? '';
unset($_SESSION['account_success'], $_SESSION['account_error']);

$user = null;
$orders = array();

$user_query = mysqli_prepare($conn, 'SELECT user_id, user_name, user_email FROM users WHERE user_id = ? LIMIT 1');

if ($user_query) {
  mysqli_stmt_bind_param($user_query, 'i', $session_user_id);
  mysqli_stmt_execute($user_query);
  $user_result = mysqli_stmt_get_result($user_query);
  $user = mysqli_fetch_assoc($user_result) ?: null;
  mysqli_stmt_close($user_query);
}

if (!$user) {
  $_SESSION = array();
  session_destroy();
  header('Location: login.php');
  exit;
}

$_SESSION['user_name'] = $user['user_name'];
$_SESSION['user_email'] = $user['user_email'];

$orders_query = mysqli_prepare($conn, 'SELECT order_id, order_status, order_cost, shipping_city, shipping_uf, shipping_address, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC');

if ($orders_query) {
  mysqli_stmt_bind_param($orders_query, 'i', $session_user_id);
  mysqli_stmt_execute($orders_query);
  $orders_result = mysqli_stmt_get_result($orders_query);

  while ($row = mysqli_fetch_assoc($orders_result)) {
    $orders[] = $row;
  }

  mysqli_stmt_close($orders_query);
}

include('layouts/header.php');
?>

<section class="account-section py-5">
  <div class="container">
    <div class="account-shell">
      <div class="account-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
          <span class="eyebrow">minha conta</span>
          <h1 class="mb-2"><?php echo htmlspecialchars($user['user_name']); ?></h1>
          <p class="account-hero__subtitle mb-0"><?php echo htmlspecialchars($user['user_email']); ?></p>
        </div>

        <form method="POST" action="account.php?user_id=<?php echo $session_user_id; ?>">
          <input type="hidden" name="action" value="logout">
          <button type="submit" class="btn btn-outline-dark rounded-pill px-4">Sair</button>
        </form>
      </div>

      <?php if ($success_message !== '') { ?>
        <div class="alert alert-success mt-4" role="alert"><?php echo htmlspecialchars($success_message); ?></div>
      <?php } ?>

      <?php if ($error_message !== '') { ?>
        <div class="alert alert-danger mt-4" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
      <?php } ?>

      <div class="row g-4 mt-1">
        <div class="col-lg-5">
          <article class="account-card h-100">
            <h2 class="h4 mb-4">Dados da conta</h2>
            <dl class="account-details mb-0">
              <div>
                <dt>Nome</dt>
                <dd><?php echo htmlspecialchars($user['user_name']); ?></dd>
              </div>
              <div>
                <dt>E-mail</dt>
                <dd><?php echo htmlspecialchars($user['user_email']); ?></dd>
              </div>
            </dl>
          </article>
        </div>

        <div class="col-lg-7">
          <article class="account-card h-100">
            <h2 class="h4 mb-4">Alterar senha</h2>
            <form method="POST" action="account.php?user_id=<?php echo $session_user_id; ?>" class="row g-3">
              <input type="hidden" name="action" value="change_password">

              <div class="col-12">
                <label for="current_password" class="form-label">Senha atual</label>
                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Digite sua senha atual">
              </div>

              <div class="col-md-6">
                <label for="new_password" class="form-label">Nova senha</label>
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Minimo de 6 caracteres">
              </div>

              <div class="col-md-6">
                <label for="confirm_new_password" class="form-label">Confirmar nova senha</label>
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Repita a nova senha">
              </div>

              <div class="col-12 d-grid d-sm-flex justify-content-sm-end">
                <button type="submit" class="btn btn-dark rounded-pill px-4">Atualizar senha</button>
              </div>
            </form>
          </article>
        </div>
      </div>

      <article class="account-card mt-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
          <div>
            <h2 class="h4 mb-1">Meus pedidos</h2>
            <p class="text-muted mb-0">Acompanhe o historico de compras realizadas na loja.</p>
          </div>
          <span class="account-orders-count"><?php echo count($orders); ?> pedido(s)</span>
        </div>

        <?php if (count($orders) === 0) { ?>
          <div class="empty-state-card text-center">
            <h3 class="h5 mb-3">Nenhum pedido encontrado</h3>
            <p class="mb-0">Assim que voce concluir uma compra, ela aparecera nesta area.</p>
          </div>
        <?php } else { ?>
          <div class="table-responsive">
            <table class="table account-orders-table align-middle mb-0">
              <thead>
                <tr>
                  <th>Pedido</th>
                  <th>Status</th>
                  <th>Data</th>
                  <th>Valor</th>
                  <th>Detalhes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order) { ?>
                  <?php $status_class = strtolower(str_replace(' ', '-', (string) $order['order_status'])); ?>
                  <tr>
                    <td>#<?php echo (int) $order['order_id']; ?></td>
                    <td>
                      <span class="order-status-badge order-status-<?php echo htmlspecialchars($status_class); ?>">
                        <?php echo htmlspecialchars(format_order_status_label($order['order_status'])); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['order_date']))); ?></td>
                    <td><?php echo htmlspecialchars(store_currency($order['order_cost'])); ?></td>
                    <td><?php echo htmlspecialchars($order['shipping_address'] . ' - ' . $order['shipping_city'] . '/' . $order['shipping_uf']); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        <?php } ?>
      </article>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>
