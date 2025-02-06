<?php
class UserManager {
    private PDO $db;

    public function __construct(string $dbName = 'user_management') {
        try {
            $dsn = "mysql:host=localhost;dbname={$dbName};charset=utf8";
            $username = "root";
            $password = "";
            $this->db = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            error_log("Connexion à la base de données réussie");
        } catch (PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            throw $e;
        }
    }

    public function addUser(string $name, string $email, string $role = 'user'): void {
        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Email invalide.");
            }
            if (!in_array($role, ['admin', 'user'])) {
                throw new InvalidArgumentException("Rôle invalide.");
            }

            $stmt = $this->db->prepare("INSERT INTO users (name, email, role) VALUES (:name, :email, :role)");
            $result = $stmt->execute(['name' => $name, 'email' => $email, 'role' => $role]);
            error_log("Ajout utilisateur - Résultat : " . ($result ? "succès" : "échec"));
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout : " . $e->getMessage());
            throw $e;
        }
    }

    public function removeUser(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getUsers(): array {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function getUser(int $id): array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        if (!$user) throw new Exception("Utilisateur introuvable.");
        return $user;
    }

    public function updateUser(int $id, string $name, string $email, string $role = null): void {
        if ($role !== null && !in_array($role, ['admin', 'user'])) {
            throw new InvalidArgumentException("Rôle invalide.");
        }

        if ($role !== null) {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email, 'role' => $role]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute(['id' => $id, 'name' => $name, 'email' => $email]);
        }
    }
}
?>
