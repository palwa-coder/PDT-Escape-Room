CREATE DATABASE IF NOT EXISTS db_escape_room;
USE db_escape_room;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings_fragment_1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    theme VARCHAR(50) NOT NULL,
    booking_time TIME NOT NULL,
    package VARCHAR(50) NOT NULL,
    booking_code VARCHAR(8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings_fragment_2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    theme VARCHAR(50) NOT NULL,
    booking_time TIME NOT NULL,
    package VARCHAR(50) NOT NULL,
    booking_code VARCHAR(8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE booking_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(8) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);