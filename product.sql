CREATE TABLE Inventory(part_ID int AUTO_INCREMENT, quantity int DEFAULT 0, PRIMARY KEY (part_ID));
CREATE TABLE Cart(part_ID int, quantity int NOT NULL, PRIMARY KEY (part_ID));

-- Some temp data added --
UPDATE Inventory SET quantity = 10 WHERE part_ID = 1;
UPDATE Inventory SET quantity = 10 WHERE part_ID = 2;
INSERT INTO Inventory VALUES (),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),(),();
INSERT INTO Cart VALUES (2,2);
