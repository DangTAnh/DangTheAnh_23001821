-- BAI 1
CREATE DATABASE IF NOT EXISTS shopping_cart CHARACTER SET utf8mb4;
USE shopping_cart;

DROP TABLE IF EXISTS cart_items;
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL
);

-- 1
INSERT INTO cart_items (name, price, quantity) VALUES
    ('Ao thun', 150000, 2),
    ('Quan jeans', 350000, 1),
    ('Giay sneaker', 800000, 1),
    ('Mu luoi trai', 120000, 8),
    ('Ao khoac', 500000, 6);

-- 2
SELECT * FROM cart_items;

-- 3
SELECT * FROM cart_items WHERE price > 100000;

-- 4
SELECT * FROM cart_items WHERE quantity > 5;

-- 5
SELECT * FROM cart_items ORDER BY price DESC;

-- 6
UPDATE cart_items SET price = 380000 WHERE name = 'Quan jeans';

-- 7
UPDATE cart_items SET quantity = 10 WHERE name = 'Mu luoi trai';

-- 8
DELETE FROM cart_items WHERE name = 'Ao khoac';

-- 9
SELECT name, price, quantity, price * quantity AS thanh_tien
FROM cart_items;

-- 10
SELECT SUM(price * quantity) AS tong_tien FROM cart_items;

-- BAI 2
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

-- 1
INSERT INTO movies (title, price, total_seats, available_seats) VALUES
    ('Avengers', 100000, 100, 93),
    ('Avatar', 120000, 80, 75),
    ('Batman', 90000, 120, 120),
    ('Dune', 110000, 90, 40),
    ('Oppenheimer', 130000, 70, 65);

-- 2
SELECT * FROM movies;

-- 3
SELECT * FROM movies WHERE price > 100000;

-- 4
SELECT * FROM movies WHERE available_seats > 50;

-- 5
SELECT * FROM movies ORDER BY price DESC;

-- 6
UPDATE movies SET available_seats = 90 WHERE title = 'Avengers';

-- 7
DELETE FROM movies WHERE title = 'Batman';

-- 8
SELECT title, total_seats - available_seats AS ve_da_ban
FROM movies;

-- 9
SELECT title, (total_seats - available_seats) * price AS doanh_thu
FROM movies;

-- 10
SELECT SUM((total_seats - available_seats) * price) AS tong_doanh_thu
FROM movies;

-- 11
SELECT *, total_seats - available_seats AS ve_da_ban
FROM movies
ORDER BY ve_da_ban DESC
LIMIT 1;
