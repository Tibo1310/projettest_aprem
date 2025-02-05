<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../BookManager.php';

class BookManagerTest extends TestCase {
    private BookManager $bookManager;
    private PDO $db;

    protected function setUp(): void {
        try {
            // Connexion directe au serveur MySQL
            $this->db = new PDO("mysql:host=localhost", "root", "");
            
            // Suppression de la base de test si elle existe
            $this->db->exec("DROP DATABASE IF EXISTS library_management_test");
            
            // Création d'une nouvelle instance de BookManager qui créera la base et la table
            $this->bookManager = new BookManager(true);
            
        } catch (PDOException $e) {
            $this->markTestSkipped('Impossible de se connecter à la base de données: ' . $e->getMessage());
        }
    }

    public function testAddBook(): void {
        $id = $this->bookManager->addBook("Le Petit Prince", "Antoine de Saint-Exupéry", 1943);
        
        $book = $this->bookManager->getBook($id);
        $this->assertNotNull($book);
        $this->assertEquals("Le Petit Prince", $book['title']);
        $this->assertEquals("Antoine de Saint-Exupéry", $book['author']);
        $this->assertEquals(1943, $book['publication_year']);
    }

    public function testUpdateBook(): void {
        $id = $this->bookManager->addBook("1984", "George Orwell", 1949);
        $this->bookManager->updateBook($id, "1984", "George Orwell", 1948);
        
        $book = $this->bookManager->getBook($id);
        $this->assertEquals(1948, $book['publication_year']);
    }

    public function testRemoveBook(): void {
        $id = $this->bookManager->addBook("Dune", "Frank Herbert", 1965);
        $this->bookManager->removeBook($id);
        
        $book = $this->bookManager->getBook($id);
        $this->assertNull($book);
    }

    public function testGetBooks(): void {
        $this->bookManager->addBook("Livre 1", "Auteur 1", 2000);
        $this->bookManager->addBook("Livre 2", "Auteur 2", 2001);
        
        $books = $this->bookManager->getBooks();
        $this->assertCount(2, $books);
    }

    public function testInvalidUpdateThrowsException(): void {
        $this->expectException(Exception::class);
        $this->bookManager->updateBook(999, "Title", "Author", 2000);
    }

    public function testInvalidDeleteThrowsException(): void {
        $this->expectException(Exception::class);
        $this->bookManager->removeBook(999);
    }

    protected function tearDown(): void {
        try {
            // Nettoyage : suppression de la base de test
            if (isset($this->db)) {
                $this->db->exec("DROP DATABASE IF EXISTS library_management_test");
            }
        } catch (PDOException $e) {
            // Log l'erreur mais ne fait pas échouer le test
            error_log("Erreur lors du nettoyage de la base de test : " . $e->getMessage());
        }
    }
} 