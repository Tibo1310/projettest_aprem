CREATE DATABASE library_management;

USE library_management;

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publication_year INT NOT NULL
);

-- Ajout de la création de la base de test
CREATE DATABASE IF NOT EXISTS library_management_test;

USE library_management_test;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publication_year INT NOT NULL
);
