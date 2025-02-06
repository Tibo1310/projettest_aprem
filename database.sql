-- Suppression de la base si elle existe
DROP DATABASE IF EXISTS user_management;

-- Création de la base de données principale
CREATE DATABASE user_management;

-- Utilisation de la base
USE user_management;

-- Création de la table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- Création de la base de test (optionnel)
CREATE DATABASE user_management_test;

USE user_management_test;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL
);
    
-- Modification de la table users pour ajouter le rôle
ALTER TABLE users ADD COLUMN role VARCHAR(50) DEFAULT 'user';