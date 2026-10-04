-- TUAN 3: THUC HANH MYSQL (ca 2 bai trong 1 file)
-- Chay bang: mysql -u root < tuan_03.sql

-- ================= BAI 1: QUAN LY GIO HANG =================
CREATE DATABASE IF NOT EXISTS shopping_cart CHARACTER SET utf8mb4;
USE shopping_cart;

DROP TABLE IF EXISTS cart_items;
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
);

-- 1. Them it nhat 5 san pham
INSERT INTO cart_items (name, price, quantity) VALUES
    ('Ao thun', 150000, 2),
    ('Quan jeans', 350000, 1),
    ('Giay sneaker', 800000, 1),
    ('Mu luoi trai', 120000, 8),
    ('Ao khoac', 500000, 6);

-- 2. Hien thi toan bo san pham
SELECT * FROM cart_items;

-- 3. San pham co gia lon hon 100000
SELECT * FROM cart_items WHERE price > 100000;

-- 4. San pham co so luong lon hon 5
SELECT * FROM cart_items WHERE quantity > 5;

-- 5. Sap xep theo gia giam dan
SELECT * FROM cart_items ORDER BY price DESC;

-- 6. Cap nhat gia cua mot san pham
UPDATE cart_items SET price = 380000 WHERE name = 'Quan jeans';

-- 7. Cap nhat so luong cua mot san pham
UPDATE cart_items SET quantity = 10 WHERE name = 'Mu luoi trai';

-- 8. Xoa mot san pham
DELETE FROM cart_items WHERE name = 'Ao khoac';

-- 9. Hien thi ten, gia, so luong va thanh tien
SELECT name, price, quantity, price * quantity AS thanh_tien
FROM cart_items;

-- 10. Tinh tong tien toan bo gio hang
SELECT SUM(price * quantity) AS tong_tien FROM cart_items;

-- ================= BAI 2: QUAN LY VE XEM PHIM =================
CREATE DATABASE IF NOT EXISTS movie_tickets CHARACTER SET utf8mb4;
USE movie_tickets;

DROP TABLE IF EXISTS movies;
CREATE TABLE movies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL
);

-- 1. Them it nhat 5 bo phim
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
    ('Avengers', 100000, 100, 93),
    ('Avatar', 120000, 80, 75),
    ('Batman', 90000, 120, 120),
    ('Dune', 110000, 90, 40),
    ('Oppenheimer', 130000, 70, 65);

-- 2. Hien thi toan bo danh sach phim
SELECT * FROM movies;

-- 3. Phim co gia ve lon hon 100000
SELECT * FROM movies WHERE price > 100000;

-- 4. Phim con nhieu hon 50 ghe
SELECT * FROM movies WHERE available_seats > 50;

-- 5. Sap xep theo gia ve giam dan
SELECT * FROM movies ORDER BY price DESC;

-- 6. Cap nhat so ghe con lai cua mot phim
UPDATE movies SET available_seats = 90 WHERE title = 'Avengers';

-- 7. Xoa mot phim
DELETE FROM movies WHERE title = 'Batman';

-- 8. So ve da ban cua tung phim
SELECT title, total_seats - available_seats AS ve_da_ban
FROM movies;

-- 9. Doanh thu cua tung phim
SELECT title, (total_seats - available_seats) * price AS doanh_thu
FROM movies;

-- 10. Tong doanh thu tat ca cac phim
SELECT SUM((total_seats - available_seats) * price) AS tong_doanh_thu
FROM movies;

-- 11. Phim co so ve ban ra nhieu nhat
SELECT *, total_seats - available_seats AS ve_da_ban
FROM movies
ORDER BY ve_da_ban DESC
LIMIT 1;
