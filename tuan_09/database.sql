CREATE DATABASE IF NOT EXISTS web_tuan_09
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE web_tuan_09;

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL CHECK (price > 0),
    stock INT NOT NULL CHECK (stock >= 0),
    image VARCHAR(255) NULL
);

-- Chỉ thêm dữ liệu mẫu khi bảng rỗng.
INSERT INTO products (name, price, stock)
SELECT sample.name, sample.price, sample.stock
FROM (
    SELECT 'Bút bi' AS name, 5000 AS price, 20 AS stock
    UNION ALL SELECT 'Vở ô ly', 12000, 15
    UNION ALL SELECT 'Thước kẻ', 8000, 0
    UNION ALL SELECT 'Bút chì', 4000, 30
    UNION ALL SELECT 'Tẩy', 3000, 25
    UNION ALL SELECT 'Hộp bút', 35000, 8
    UNION ALL SELECT 'Bìa hồ sơ', 6000, 40
    UNION ALL SELECT 'Kéo', 15000, 12
    UNION ALL SELECT 'Keo dán', 10000, 10
    UNION ALL SELECT 'Sổ tay', 25000, 18
    UNION ALL SELECT 'Bút đánh dấu', 18000, 6
    UNION ALL SELECT 'Giấy ghi chú', 16000, 14
) AS sample
WHERE NOT EXISTS (SELECT 1 FROM products);
