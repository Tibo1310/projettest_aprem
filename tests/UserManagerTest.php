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
            $this->db->exec("DROP DATABASE IF EXISTS user_management");
            $this->db->exec("CREATE DATABASE user_management");
            $this->db->exec("USE user_management");
            
            // Recréation de la table users avec le champ role
            $this->db->exec("DROP TABLE IF EXISTS users");
            $this->db->exec("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                email VARCHAR(100) NOT NULL,
                role VARCHAR(50) DEFAULT 'user'
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
        $this->assertEquals("user", $users[0]['role']); // Vérifier le rôle par défaut
    }

    public function testAddUserEmailException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->userManager->addUser("John Doe", "invalid-email");
    }

    public function testUpdateUser(): void {
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        $id = $users[0]['id'];
        
        $this->userManager->updateUser($id, "Jane Doe", "jane@example.com", "admin");
        $updatedUser = $this->userManager->getUser($id);
        
        $this->assertEquals("Jane Doe", $updatedUser['name']);
        $this->assertEquals("jane@example.com", $updatedUser['email']);
        $this->assertEquals("admin", $updatedUser['role']);
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
        $this->userManager->addUser("User 2", "user2@example.com", "admin");
        
        $users = $this->userManager->getUsers();
        $this->assertCount(2, $users);
        
        // Vérifier que le premier utilisateur a le rôle par défaut
        $this->assertEquals("user", $users[0]['role']);
        // Vérifier que le second utilisateur a le rôle admin
        $this->assertEquals("admin", $users[1]['role']);
    }

    public function testInvalidUpdateThrowsException(): void {
        $this->expectException(Exception::class);
        $this->userManager->updateUser(999, "Test User", "test@example.com");
    }

    public function testInvalidDeleteThrowsException(): void {
        $this->expectException(Exception::class);
        $this->userManager->removeUser(999);
    }

    protected function tearDown(): void {
        try {
            if (isset($this->db)) {
                $this->db->exec("TRUNCATE TABLE users");
            }
        } catch (PDOException $e) {
            error_log("Erreur lors du nettoyage de la table : " . $e->getMessage());
        }
    }
} 