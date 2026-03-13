CREATE DATABASE IF NOT EXISTS project_db;

USE project_db;

CREATE TABLE IF NOT EXISTS products (
  product_id INT(11) NOT NULL AUTO_INCREMENT,
  product_name VARCHAR(100) NOT NULL,
  product_category VARCHAR(100) NOT NULL,
  product_description VARCHAR(250) NOT NULL,
  product_image VARCHAR(250) NOT NULL,
  product_image2 VARCHAR(250) NOT NULL,
  product_image3 VARCHAR(250) NOT NULL,
  product_image4 VARCHAR(250) NOT NULL,
  product_price DECIMAL(6,2) NOT NULL,
  product_special_offer INT(2) NOT NULL DEFAULT 0,
  product_color VARCHAR(100) NOT NULL,
  PRIMARY KEY (product_id)
);

CREATE TABLE IF NOT EXISTS users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  user_name VARCHAR(100) NOT NULL,
  user_email VARCHAR(100) NOT NULL,
  user_password VARCHAR(100) NOT NULL,
  PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS orders (
  order_id INT(11) NOT NULL AUTO_INCREMENT,
  order_cost DECIMAL(6,2) NOT NULL,
  order_status VARCHAR(100) NOT NULL,
  user_id INT(11) NOT NULL,
  shipping_city VARCHAR(255) NOT NULL,
  shipping_uf VARCHAR(2) NOT NULL,
  shipping_address VARCHAR(255) NOT NULL,
  order_date DATETIME NOT NULL,
  PRIMARY KEY (order_id),
  CONSTRAINT fk_orders_users
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS order_items (
  item_id INT(11) NOT NULL AUTO_INCREMENT,
  order_id INT(11) NOT NULL,
  product_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  qnt INT(11) NOT NULL,
  order_date DATETIME NOT NULL,
  PRIMARY KEY (item_id),
  CONSTRAINT fk_order_items_orders
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
  CONSTRAINT fk_order_items_products
    FOREIGN KEY (product_id) REFERENCES products(product_id),
  CONSTRAINT fk_order_items_users
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS payments (
  payment_id INT(11) NOT NULL AUTO_INCREMENT,
  order_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  transaction_id VARCHAR(255) NOT NULL,
  PRIMARY KEY (payment_id),
  CONSTRAINT fk_payments_orders
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
  CONSTRAINT fk_payments_users
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS admins (
  admin_id INT(11) NOT NULL AUTO_INCREMENT,
  admin_email VARCHAR(255) NOT NULL,
  admin_name VARCHAR(255) NOT NULL,
  admin_password VARCHAR(100) NOT NULL,
  PRIMARY KEY (admin_id)
);

INSERT INTO admins (admin_id, admin_email, admin_name, admin_password)
VALUES (NULL, 'admin@shop.com.br', 'admin', 'e10adc3949ba59abbe56e057f20f883e');
