-- Sunrise Hotel Management System database
-- Import this file in phpMyAdmin after creating a database.

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','guest') NOT NULL DEFAULT 'guest'
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    type ENUM('Single','Double','Deluxe','Suite') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('booked','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'booked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- passwords are hashed (admin123 and user123)
INSERT INTO users (username, full_name, password, role) VALUES
('admin', 'Hotel Admin',    '$2y$12$LRo2Esfd9MzrsBbkPRMWzesgLZyHBN/bBD/vDySIdbsBUPbIkKUze', 'admin'),
('juan',  'Juan Dela Cruz', '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'guest'),
('maria', 'Maria Santos',   '$2y$12$6UccfWGz17TXyVNkECTLseIwc9Imt0NFEHfQs1OQMZ3zbkOI2ocKG', 'guest');

INSERT INTO rooms (room_number, type, price, capacity) VALUES
('101', 'Single', 1200.00, 1),
('102', 'Single', 1200.00, 1),
('201', 'Double', 2000.00, 2),
('202', 'Double', 2000.00, 2),
('301', 'Deluxe', 3200.00, 3),
('401', 'Suite',  5000.00, 4);

INSERT INTO bookings (user_id, room_id, check_in, check_out, total_price, status) VALUES
(2, 3, DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 4000.00, 'booked'),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), 2400.00, 'checked_out'),
(3, 5, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 9600.00, 'checked_in');
