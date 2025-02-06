CREATE DATABASE user_management;

USE user_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'user'
);
