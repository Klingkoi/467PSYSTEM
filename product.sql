-- Drop existing tables if they exist
DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS inventory, orders;
DROP TABLE IF EXISTS shipping_charges, admins;

-- Legacy DB used for parts table and customers table

-- Create inventory table
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    part_number INT NOT NULL,
    quantity_on_hand INT NOT NULL DEFAULT 0
);

-- Create orders table
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

-- Create order_details table
CREATE TABLE order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    part_number INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- Create shipping_charges table
CREATE TABLE shipping_charges (
    charge_id INT AUTO_INCREMENT PRIMARY KEY,
    weight_lower_bound FLOAT(5, 2) NOT NULL,
    weight_upper_bound FLOAT(5, 2) NOT NULL,
    charge FLOAT(10, 2) NOT NULL
);

-- Create admins table
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

-- Arbitrary sample data

-- Insert sample data into inventory table
INSERT INTO inventory (part_number, quantity_on_hand) VALUES
(1, 100), (2, 150), (3, 200), (4, 50), (5, 75), (6, 120),
(7, 95), (8, 180), (9, 130), (10, 60), (11, 170), (12, 140),
(13, 125), (14, 80), (15, 110), (16, 155), (17, 105), (18, 135),
(19, 160), (20, 90), (21, 70), (22, 145), (23, 115), (24, 195),
(25, 185), (26, 65), (27, 175), (28, 85), (29, 100), (30, 200),
(31, 50), (32, 150), (33, 75), (34, 125), (35, 140), (36, 155),
(37, 95), (38, 120), (39, 130), (40, 60), (41, 110), (42, 180),
(43, 135), (44, 170), (45, 80), (46, 160), (47, 115), (48, 145),
(49, 105), (50, 195), (51, 185), (52, 65), (53, 175), (54, 85),
(55, 100), (56, 150), (57, 125), (58, 120), (59, 135), (60, 195),
(61, 200), (62, 70), (63, 75), (64, 145), (65, 185), (66, 155),
(67, 50), (68, 180), (69, 170), (70, 85), (71, 105), (72, 95),
(73, 110), (74, 140), (75, 80), (76, 100), (77, 150), (78, 125),
(79, 120), (80, 135), (81, 195), (82, 200), (83, 70), (84, 75),
(85, 145), (86, 185), (87, 155), (88, 50), (89, 180), (90, 170),
(91, 85), (92, 105), (93, 95), (94, 110), (95, 140), (96, 80),
(97, 100), (98, 150), (99, 200), (100, 60), (101, 125), (102, 120),
(103, 135), (104, 195), (105, 185), (106, 65), (107, 175), (108, 85),
(109, 115), (110, 145), (111, 105), (112, 195), (113, 185), (114, 160),
(115, 140), (116, 150), (117, 90), (118, 120), (119, 180), (120, 200),
(121, 75), (122, 110), (123, 95), (124, 125), (125, 85), (126, 145),
(127, 140), (128, 100), (129, 170), (130, 80), (131, 155), (132, 160),
(133, 115), (134, 70), (135, 195), (136, 105), (137, 120), (138, 145),
(139, 185), (140, 135), (141, 65), (142, 175), (143, 200), (144, 95),
(145, 50), (146, 125), (147, 130), (148, 100), (149, 155);

-- Insert sample data into orders table
INSERT INTO orders (customer_id, total_price, total_weight, shipping_cost, order_status) VALUES
(1, 45.97, 3.5, 5.99, 'Authorized'),
(2, 12.98, 0.2, 2.99, 'Authorized'),
(3, 25.67, 3.0, 3.99, 'Authorized'),
(4, 75.25, 5.0, 6.99, 'Authorized'),
(5, 25.50, 1.0, 3.99, 'Authorized'),
(6, 150.75, 8.5, 11.99, 'Authorized'),
(7, 33.20, 2.3, 4.99, 'Authorized'),
(8, 90.50, 7.0, 9.99, 'Authorized'),
(9, 55.10, 4.2, 5.99, 'Authorized'),
(10, 40.75, 3.1, 5.99, 'Authorized'),
(1, 80.20, 6.0, 7.99, 'Authorized'),
(2, 19.99, 0.5, 2.99, 'Authorized'),
(3, 99.99, 10.0, 10.99, 'Authorized'),
(4, 65.40, 4.8, 6.99, 'Authorized'),
(5, 14.50, 0.3, 2.99, 'Authorized'),
(6, 130.80, 9.0, 10.99, 'Authorized'),
(7, 45.30, 3.7, 5.99, 'Authorized'),
(8, 87.20, 6.5, 8.99, 'Authorized'),
(9, 30.99, 2.0, 4.99, 'Authorized'),
(10, 110.75, 8.2, 11.99, 'Authorized'),
(1, 57.60, 4.5, 6.99, 'Authorized'),
(2, 23.50, 1.2, 3.99, 'Authorized'),
(3, 175.25, 10.0, 11.99, 'Authorized'),
(4, 50.80, 3.9, 6.99, 'Authorized'),
(5, 35.70, 2.8, 4.99, 'Authorized'),
(6, 95.90, 7.3, 9.99, 'Authorized'),
(7, 67.50, 5.2, 7.99, 'Authorized'),
(8, 28.99, 1.8, 4.99, 'Authorized'),
(9, 145.20, 9.5, 10.99, 'Authorized'),
(10, 62.40, 4.7, 7.99, 'Authorized');

-- Insert sample data into order_details table
INSERT INTO order_details (order_id, part_number, quantity) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 2, 1),
(3, 4, 1),
(4, 1, 2),
(5, 3, 3),
(6, 2, 4),
(7, 1, 1),
(8, 4, 2),
(9, 3, 1),
(10, 2, 3),
(11, 1, 2),
(12, 4, 1),
(13, 3, 2),
(14, 2, 4),
(15, 1, 3),
(16, 4, 1),
(17, 3, 2),
(18, 2, 3),
(19, 1, 1),
(20, 4, 2),
(21, 3, 3),
(22, 2, 4),
(23, 1, 1),
(24, 4, 2),
(25, 3, 1),
(26, 2, 3),
(27, 1, 2),
(28, 4, 1),
(29, 3, 2),
(30, 2, 3);

-- Insert sample data into shipping_charges table
INSERT INTO shipping_charges (weight_lower_bound, weight_upper_bound, charge) VALUES
(0, 1, 2.99),
(1, 5, 5.99),
(5, 20, 10.99);

-- Insert sample data into admins table
INSERT INTO admins (username, password_hash) VALUES
('admin1', 'hashed_password_1'),
('admin2', 'hashed_password_2');

------------------------------------------------------------------
-- Drop existing table if it exists
DROP TABLE IF EXISTS brackets;

-- Create brackets table
CREATE TABLE brackets (
    bracket_id INT AUTO_INCREMENT PRIMARY KEY,
    bracket_lower FLOAT(7, 2) NOT NULL,
    bracket_upper FLOAT(7, 2),
    cost FLOAT(10, 2) NOT NULL
);

-- Insert sample data into brackets table
INSERT INTO brackets (bracket_lower, bracket_upper, cost) VALUES
(0, 5, 0.00),
(5, 25, 1.50),
(25, 50, 10.00),
(50, 100, 15.00),
(100, 500, 30.00),
(500, 1000, 125.99),
(1000, 2000, 250.99),
(2000, 3000, 350.99),
(3000, 3500, 500.00),
(3500, NULL, 400.00);



