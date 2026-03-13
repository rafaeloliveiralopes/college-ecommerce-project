<?php
include('header.php');
ensure_admin_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  admin_redirect('add_product.php');
}

$product_name = trim($_POST['product_name'] ?? '');
$product_category = trim($_POST['product_category'] ?? '');
$product_description = trim($_POST['product_description'] ?? '');
$product_price = (float) ($_POST['product_price'] ?? 0);
$product_special_offer = (int) ($_POST['product_special_offer'] ?? 0);
$product_color = trim($_POST['product_color'] ?? '');
$error_message = '';

if ($product_name === '' || $product_category === '' || $product_description === '' || $product_color === '') {
  $error_message = 'Preencha todos os campos obrigatórios do produto.';
} elseif ($product_price <= 0) {
  $error_message = 'Informe um preço válido para o produto.';
} elseif ($product_special_offer < 0 || $product_special_offer > 99) {
  $error_message = 'A oferta deve ficar entre 0 e 99.';
}

$uploaded_image = null;

if ($error_message === '') {
  $uploaded_image = admin_upload_image($_FILES['product_image'] ?? null, $error_message);
}

if ($error_message !== '') {
  admin_set_flash('danger', $error_message);
  admin_redirect('add_product.php');
}

$insert_product = mysqli_prepare($conn, 'INSERT INTO products (product_name, product_category, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

if (!$insert_product) {
  admin_set_flash('danger', 'Não foi possível preparar o cadastro do produto.');
  admin_redirect('add_product.php');
}

$secondary_image = $uploaded_image;
mysqli_stmt_bind_param($insert_product, 'sssssssdis', $product_name, $product_category, $product_description, $uploaded_image, $secondary_image, $secondary_image, $secondary_image, $product_price, $product_special_offer, $product_color);
mysqli_stmt_execute($insert_product);
$created = mysqli_stmt_affected_rows($insert_product) > 0;
mysqli_stmt_close($insert_product);

if ($created) {
  admin_set_flash('success', 'Produto criado com sucesso.');
  admin_redirect('products.php');
}

admin_set_flash('danger', 'Não foi possível criar o produto.');
admin_redirect('add_product.php');
?>
