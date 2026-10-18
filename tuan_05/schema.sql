CREATE DATABASE IF NOT EXISTS shopping_cart CHARACTER SET utf8mb4;
USE shopping_cart;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
);

INSERT INTO products (name, price, quantity) VALUES
    ('Ao thun', 150000, 2),
    ('Quan jeans', 350000, 1),
    ('Giay sneaker', 800000, 1),
    ('Mu luoi trai', 120000, 8),
    ('Ao khoac', 500000, 6);

-- Bang users (dang ky / dang nhap)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tai khoan mau: admin / 123456 (hash thuc sinh bang password_hash)
INSERT INTO users (username, password) VALUES
    ('admin', '$2y$10$Erq39Y/QW2.FNFkH6FYCB.bmR/6smOyyWIFnSMRtSBcBOqnY9TiGu');
