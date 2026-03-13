<?php include('header.php'); ?>
<?php
ensure_admin_session();

$page = max(1, (int) ($_GET['page'] ?? 1));
$items_per_page = 5;
list($total_users, $total_pages, $page, $offset) = admin_paginate($conn, 'users', $page, $items_per_page);

$users = array();
$users_query = mysqli_prepare($conn, 'SELECT user_id, user_name, user_email FROM users ORDER BY user_id ASC LIMIT ? OFFSET ?');

if ($users_query) {
  mysqli_stmt_bind_param($users_query, 'ii', $items_per_page, $offset);
  mysqli_stmt_execute($users_query);
  $result = mysqli_stmt_get_result($users_query);

  while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
  }

  mysqli_stmt_close($users_query);
}
?>
<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between flex-wrap align-items-center mb-4">
        <div>
          <h1 class="h2 mb-1">Contas</h1>
          <p class="text-muted mb-0">Gestão das contas dos clientes cadastrados.</p>
        </div>
      </div>

      <div class="card content-card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>ID do Usuário</th>
                  <th>Nome</th>
                  <th>Email</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($users) === 0) { ?>
                  <tr>
                    <td colspan="3" class="text-center py-4">Nenhum usuário encontrado no banco de dados.</td>
                  </tr>
                <?php } ?>

                <?php foreach ($users as $user) { ?>
                  <tr>
                    <td><?php echo (int) $user['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php admin_render_pagination('account.php', $page, $total_pages); ?>
    </main>
  </div>
</div>
</body>

</html>
