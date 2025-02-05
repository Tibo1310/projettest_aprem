<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../UserManager.php';

class UserManagerTest extends TestCase {
    private UserManager $userManager;
    private PDO $db;

    protected function setUp(): void {
        try {
            // Connexion directe au serveur MySQL
            $this->db = new PDO("mysql:host=localhost", "root", "");
            
            // Création de la base si elle n'existe pas
            $this->db->exec("CREATE DATABASE IF NOT EXISTS user_management");
            $this->db->exec("USE user_management");
            
            // Recréation de la table users
            $this->db->exec("DROP TABLE IF EXISTS users");
            $this->db->exec("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                email VARCHAR(100) NOT NULL
            )");

            $this->userManager = new UserManager();
            
        } catch (PDOException $e) {
            $this->markTestSkipped('Impossible de se connecter à la base de données: ' . $e->getMessage());
        }
    }

    public function testAddUser(): void {
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        
        $this->assertCount(1, $users);
        $this->assertEquals("John Doe", $users[0]['name']);
        $this->assertEquals("john@example.com", $users[0]['email']);
    }

    public function testUpdateUser(): void {
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        $id = $users[0]['id'];
        
        $this->userManager->updateUser($id, "Jane Doe", "jane@example.com");
        $updatedUser = $this->userManager->getUser($id);
        
        $this->assertEquals("Jane Doe", $updatedUser['name']);
        $this->assertEquals("jane@example.com", $updatedUser['email']);
    }

    public function testRemoveUser(): void {
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        $id = $users[0]['id'];
        
        $this->userManager->removeUser($id);
        $users = $this->userManager->getUsers();
        
        $this->assertCount(0, $users);
    }

    public function testGetUsers(): void {
        $this->userManager->addUser("User 1", "user1@example.com");
        $this->userManager->addUser("User 2", "user2@example.com");
        
        $users = $this->userManager->getUsers();
        $this->assertCount(2, $users);
    }

    public function testInvalidEmail(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->userManager->addUser("John Doe", "invalid-email");
    }

    protected function tearDown(): void {
        try {
            if (isset($this->db)) {
                // On vide juste la table au lieu de supprimer la base
                $this->db->exec("TRUNCATE TABLE users");
            }
        } catch (PDOException $e) {
            error_log("Erreur lors du nettoyage de la table : " . $e->getMessage());
        }
    }
} 