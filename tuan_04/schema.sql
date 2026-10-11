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
