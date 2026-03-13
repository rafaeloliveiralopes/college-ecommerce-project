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
  (1, 'Camiseta Essential', 'Moda', 'Camiseta basica em algodao com modelagem confortavel.', 'camiseta-essencial-69b433073b1a7.jpeg', 'camiseta-essencial-69b433073b31a.jpeg', 'camiseta-essencial-69b433073b39d.jpeg', 'camiseta-essencial-69b433073b410.jpeg', 79.90, 10, 'Branco'),
  (2, 'Tenis Urban Street', 'Calcados', 'Tenis casual para uso diario com solado emborrachado.', 'tennis-urban-street-69b432b3f08f1.jpeg', 'tennis-urban-street-69b432b3f0a2e.jpeg', 'tennis-urban-street-69b432b3f0aab.jpeg', 'tennis-urban-street-69b432b3f0b1f.jpeg', 249.90, 15, 'Preto'),
  (3, 'Mochila Venture', 'Acessorios', 'Mochila com divisorias internas e tecido resistente a agua.', 'mochila-venture-69b4325b4547e.jpeg', 'mochila-venture-69b4325b45549.jpeg', 'mochila-venture-69b4325b455b2.jpeg', 'mochila-venture-69b4325b45605.jpeg', 189.90, 5, 'Cinza'),
  (4, 'Relogio Minimal', 'Acessorios', 'Relogio com pulseira em couro sintetico e design minimalista.', 'relogio-minimal-69b43225f0f6b.webp', 'relogio-minimal-69b43225f10c6.webp', 'relogio-minimal-69b43225f1151.webp', 'relogio-minimal-69b43225f11cb.webp', 159.90, 0, 'Marrom'),
  (5, 'Jaqueta Wind', 'Moda', 'Jaqueta leve com fechamento em ziper e capuz removivel.', 'jaqueta-wind-69b431f334db6.webp', 'jaqueta-wind-69b431f334ec7.webp', 'jaqueta-wind-69b431f334f62.webp', 'jaqueta-wind-69b431f334fd9.webp', 299.90, 20, 'Azul'),
  (6, 'Fone Pulse Pro', 'Eletronicos', 'Fone bluetooth com cancelamento passivo de ruido.', 'phone-pulse-pro-69b431920e9fd.webp', 'phone-pulse-pro-69b431b4655e3.webp', 'phone-pulse-pro-69b431b4656d4.webp', 'phone-pulse-pro-69b431b46575e.webp', 219.90, 8, 'Grafite');

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
