<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../UserManager.php';

class UserManagerRegressionTest extends TestCase {
    private UserManager $userManager;
    private PDO $db;

    protected function setUp(): void {
        try {
            $this->db = new PDO("mysql:host=localhost", "root", "");
            
            // Supprime la base de test si elle existe
            $this->db->exec("DROP DATABASE IF EXISTS user_management_test");
            
            // Crée la base de test
            $this->db->exec("CREATE DATABASE user_management_test");
            $this->db->exec("USE user_management_test");
            
            // Crée la table avec la colonne role
            $this->db->exec("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                email VARCHAR(100) NOT NULL,
                role VARCHAR(50) DEFAULT 'user'
            )");

            // Modifie la connexion pour utiliser la base de test
            $this->userManager = new UserManager('user_management_test');
            
        } catch (PDOException $e) {
            $this->markTestSkipped('Impossible de se connecter à la base de données: ' . $e->getMessage());
        }
    }

    // Test de non-régression pour l'ajout d'utilisateur
    public function testAddUserRegression(): void {
        // Test de l'ancien comportement (sans spécifier de rôle)
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        
        $this->assertCount(1, $users);
        $this->assertEquals("John Doe", $users[0]['name']);
        $this->assertEquals("john@example.com", $users[0]['email']);
        $this->assertEquals("user", $users[0]['role']); // Vérifie que le rôle par défaut est 'user'

        // Test du nouveau comportement avec rôle spécifié
        $this->userManager->addUser("Admin User", "admin@example.com", "admin");
        $users = $this->userManager->getUsers();
        
        $this->assertCount(2, $users);
        $adminUser = array_filter($users, fn($u) => $u['email'] === "admin@example.com");
        $adminUser = reset($adminUser);
        $this->assertEquals("admin", $adminUser['role']);
    }

    // Test de non-régression pour la mise à jour
    public function testUpdateUserRegression(): void {
        // Création d'un utilisateur de test
        $this->userManager->addUser("John Doe", "john@example.com");
        $users = $this->userManager->getUsers();
        $id = $users[0]['id'];
        
        // Test de l'ancien comportement de mise à jour (sans modifier le rôle)
        $this->userManager->updateUser($id, "Jane Doe", "jane@example.com");
        $updatedUser = $this->userManager->getUser($id);
        
        $this->assertEquals("Jane Doe", $updatedUser['name']);
        $this->assertEquals("jane@example.com", $updatedUser['email']);
        $this->assertEquals("user", $updatedUser['role']); // Le rôle ne devrait pas changer

        // Test du nouveau comportement avec mise à jour du rôle
        $this->userManager->updateUser($id, "Jane Admin", "jane.admin@example.com", "admin");
        $updatedUser = $this->userManager->getUser($id);
        
        $this->assertEquals("Jane Admin", $updatedUser['name']);
        $this->assertEquals("jane.admin@example.com", $updatedUser['email']);
        $this->assertEquals("admin", $updatedUser['role']);
    }

    // Test de validation du rôle
    public function testInvalidRoleThrowsException(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->userManager->addUser("Test User", "test@example.com", "invalid_role");
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