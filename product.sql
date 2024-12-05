DROP TABLE IF EXISTS inventory, customers, orders, order_details, shipping_charges, admins, Brackets;
DROP TABLE IF EXISTS parts;
--
CREATE TABLE parts (
    number INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(100) NOT NULL,
    price FLOAT(8, 2) NOT NULL,
    weight FLOAT(4, 2) NOT NULL,
    pictureURL VARCHAR(255)
);
--
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    part_number INT NOT NULL,
    quantity_on_hand INT NOT NULL DEFAULT 0,
    FOREIGN KEY (part_number) REFERENCES parts(number)
);
--
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    address TEXT NOT NULL
);
--
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price FLOAT(10, 2) NOT NULL,
    shipping_cost FLOAT(10, 2) NOT NULL,
    order_status ENUM('Authorized', 'Packed', 'Shipped', 'Completed') DEFAULT 'Authorized',
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);
--
CREATE TABLE order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    part_number INT NOT NULL,
    quantity INT NOT NULL,
    price_per_item FLOAT(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (part_number) REFERENCES parts(number)
);
--

CREATE TABLE Brackets (
	bracket_upper INT PRIMARY KEY,
	cost float(6,2) NOT NULL);

INSERT INTO Brackets VALUES(0, 0);
--
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);
--
