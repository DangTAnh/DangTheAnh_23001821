CREATE DATABASE IF NOT EXISTS web_tuan_08
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE web_tuan_08;

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL CHECK (price > 0),
    stock INT NOT NULL CHECK (stock >= 0)
);

-- Chỉ thêm dữ liệu mẫu khi bảng đang rỗng, không xóa dữ liệu đã có.
INSERT INTO products (name, price, stock)
SELECT sample.name, sample.price, sample.stock
FROM (
    SELECT 'Bút bi' AS name, 5000 AS price, 20 AS stock
    UNION ALL SELECT 'Vở ô ly', 12000, 15
    UNION ALL SELECT 'Thước kẻ', 8000, 0
) AS sample
WHERE NOT EXISTS (SELECT 1 FROM products);
