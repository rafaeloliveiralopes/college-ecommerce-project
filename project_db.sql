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

INSERT INTO users (user_id, user_name, user_email, user_password) VALUES
  (1, 'Mariana Costa', 'mariana@example.com', 'e10adc3949ba59abbe56e057f20f883e'),
  (2, 'Lucas Ferreira', 'lucas@example.com', 'e10adc3949ba59abbe56e057f20f883e'),
  (3, 'Camila Souza', 'camila@example.com', 'e10adc3949ba59abbe56e057f20f883e');

INSERT INTO products (
  product_id,
  product_name,
  product_category,
  product_description,
  product_image,
  product_image2,
  product_image3,
  product_image4,
  product_price,
  product_special_offer,
  product_color
) VALUES
  (1, 'Camiseta Essential', 'Moda', 'Camiseta basica em algodao com modelagem confortavel.', 'product-1.jpg', 'product-1-2.jpg', 'product-1-3.jpg', 'product-1-4.jpg', 79.90, 10, 'Branco'),
  (2, 'Tenis Urban Street', 'Calcados', 'Tenis casual para uso diario com solado emborrachado.', 'product-2.jpg', 'product-2-2.jpg', 'product-2-3.jpg', 'product-2-4.jpg', 249.90, 15, 'Preto'),
  (3, 'Mochila Venture', 'Acessorios', 'Mochila com divisorias internas e tecido resistente a agua.', 'product-3.jpg', 'product-3-2.jpg', 'product-3-3.jpg', 'product-3-4.jpg', 189.90, 5, 'Cinza'),
  (4, 'Relogio Minimal', 'Acessorios', 'Relogio com pulseira em couro sintetico e design minimalista.', 'product-4.jpg', 'product-4-2.jpg', 'product-4-3.jpg', 'product-4-4.jpg', 159.90, 0, 'Marrom'),
  (5, 'Jaqueta Wind', 'Moda', 'Jaqueta leve com fechamento em ziper e capuz removivel.', 'product-5.jpg', 'product-5-2.jpg', 'product-5-3.jpg', 'product-5-4.jpg', 299.90, 20, 'Azul'),
  (6, 'Fone Pulse Pro', 'Eletronicos', 'Fone bluetooth com cancelamento passivo de ruido.', 'product-6.jpg', 'product-6-2.jpg', 'product-6-3.jpg', 'product-6-4.jpg', 219.90, 8, 'Grafite');

INSERT INTO orders (
  order_id,
  order_cost,
  order_status,
  user_id,
  shipping_city,
  shipping_uf,
  shipping_address,
  order_date
) VALUES
  (1, 79.90, 'on_hold', 1, 'Sao Paulo', 'SP', 'Rua das Flores, 120', '2026-03-01 10:15:00'),
  (2, 249.90, 'paid', 2, 'Campinas', 'SP', 'Av. Brasil, 450', '2026-03-02 14:20:00'),
  (3, 379.80, 'shipped', 3, 'Belo Horizonte', 'MG', 'Rua da Bahia, 880', '2026-03-03 09:05:00'),
  (4, 159.90, 'delivered', 1, 'Curitiba', 'PR', 'Alameda Cabral, 33', '2026-03-04 16:40:00'),
  (5, 299.90, 'paid', 2, 'Rio de Janeiro', 'RJ', 'Rua do Catete, 77', '2026-03-05 11:30:00'),
  (6, 439.80, 'on_hold', 3, 'Porto Alegre', 'RS', 'Av. Ipiranga, 1500', '2026-03-06 13:10:00');

INSERT INTO order_items (
  item_id,
  order_id,
  product_id,
  user_id,
  qnt,
  order_date
) VALUES
  (1, 1, 1, 1, 1, '2026-03-01 10:15:00'),
  (2, 2, 2, 2, 1, '2026-03-02 14:20:00'),
  (3, 3, 3, 3, 2, '2026-03-03 09:05:00'),
  (4, 4, 4, 1, 1, '2026-03-04 16:40:00'),
  (5, 5, 5, 2, 1, '2026-03-05 11:30:00'),
  (6, 6, 6, 3, 2, '2026-03-06 13:10:00');

INSERT INTO payments (payment_id, order_id, user_id, transaction_id) VALUES
  (1, 2, 2, 'TESTPAY-0002'),
  (2, 5, 2, 'TESTPAY-0005');
