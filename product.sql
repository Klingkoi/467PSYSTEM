DROP TABLE IF EXISTS order_details; -- order_details references parts and orders
DROP TABLE IF EXISTS inventory, orders; -- inventory references parts, orders references customers
DROP TABLE IF EXISTS parts, customers, shipping_charges, admins;
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
--total_price will INCLUDE shipping
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_price FLOAT(10, 2) NOT NULL, 
    total_weight FLOAT(4, 2) NOT NULL,
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
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (part_number) REFERENCES parts(number)
);
--
CREATE TABLE shipping_charges (
    charge_id INT AUTO_INCREMENT PRIMARY KEY,
    weight_lower_bound FLOAT(5, 2) NOT NULL,
    weight_upper_bound FLOAT(5, 2) NOT NULL,
    charge FLOAT(10, 2) NOT NULL
);
--
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

--------------------------------------------------------- Sample Data from ChatGPT (can likely be removed when we're done)

-- Insert sample data into parts table
INSERT INTO parts (description, price, weight, pictureURL) VALUES
('Windshield', 29.99, 2.5, 'https://blitz.cs.niu.edu/pics/shi.jpg'),
('Windshield Wipers', 9.99, 0.5, 'https://blitz.cs.niu.edu/pics/wip.jpg'),
('Solenoid', 4.99, 0.1, 'https://blitz.cs.niu.edu/pics/sol.jpg'),
('Harness', 89.99, 15.0, 'https://blitz.cs.niu.edu/pics/har.jpg');

-- Insert sample data into inventory table
INSERT INTO inventory (part_number, quantity_on_hand) VALUES
(1, 100),
(2, 200),
(3, 150),
(4, 50);

-- Insert sample data into customers table
INSERT INTO customers (name, email, address) VALUES
('John Doe', 'john.doe@example.com', '123 Main St, Springfield, IL'),
('Jane Smith', 'jane.smith@example.com', '456 Elm St, Springfield, IL'),
('Emily Johnson', 'emily.johnson@example.com', '789 Oak St, Springfield, IL');

-- Insert sample data into orders table
INSERT INTO orders (customer_id, total_price, total_weight, shipping_cost, order_status) VALUES
(1, 45.97, 3.5, 5.99, 'Authorized'),
(2, 12.98, 0.2, 2.99, 'Authorized'),
(3, 100.98, 10.2, 10.99, 'Shipped');

-- Insert sample data into order_details table
INSERT INTO order_details (order_id, part_number, quantity) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 2, 1),
(3, 4, 1);

-- Insert sample data into shipping_charges table
INSERT INTO shipping_charges (weight_lower_bound, weight_upper_bound, charge) VALUES
(0, 1, 2.99),
(1, 5, 5.99),
(5, 20, 10.99);

-- Insert sample data into admins table
INSERT INTO admins (username, password_hash) VALUES
('admin1', 'hashed_password_1'),
('admin2', 'hashed_password_2');
