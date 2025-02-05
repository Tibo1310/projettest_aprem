<?php
class BookManager {
    private PDO $db;

    public function __construct($isTest = false) {
        $dbname = $isTest ? "library_management_test" : "library_management";
        try {
            // Tentative de connexion à la base de données
            $dsn = "mysql:host=localhost;charset=utf8";
            $username = "root";
            $password = "";  // Modifié pour correspondre à la configuration XAMPP par défaut
            $this->db = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Création de la base si elle n'existe pas
            $this->db->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            $this->db->exec("USE $dbname");
            
            // Création de la table si elle n'existe pas
            $this->db->exec("CREATE TABLE IF NOT EXISTS books (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(200) NOT NULL,
                author VARCHAR(100) NOT NULL,
                publication_year INT NOT NULL
            )");
            
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function addBook(string $title, string $author, int $publicationYear): int {
        if (empty($title) || empty($author) || $publicationYear <= 0) {
            throw new InvalidArgumentException("Données du livre invalides.");
        }

        $stmt = $this->db->prepare("INSERT INTO books (title, author, publication_year) VALUES (:title, :author, :year)");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'year' => $publicationYear
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    public function updateBook(int $id, string $title, string $author, int $publicationYear): void {
        $book = $this->getBook($id);
        if (!$book) {
            throw new Exception("Livre introuvable.");
        }

        $stmt = $this->db->prepare("UPDATE books SET title = :title, author = :author, publication_year = :year WHERE id = :id");
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'year' => $publicationYear
        ]);
    }

    public function removeBook(int $id): void {
        $book = $this->getBook($id);
        if (!$book) {
            throw new Exception("Livre introuvable.");
        }

        $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getBooks(): array {
        $stmt = $this->db->query("SELECT * FROM books");
        return $stmt->fetchAll();
    }

    public function getBook(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
?> 