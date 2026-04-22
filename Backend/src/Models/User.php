<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : User.php
 * But : Classe User avec CRUD pour gérer les utilisateurs du site web
 */
namespace App\Models;

use App\Models\PDOSingleton;
use PDO;

/**
 * Summary of User classe user 
 */
class User {
    public $id;
    public $email;
    public $login;
    public $password;
    public $token;

    public function __construct($id, $login, $email, $password, $token) {
        $this->id = $id;
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }

    /**
     * Summary of Create permet de créer un user dans la base de données
     * @return void
     */
    public function Create() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("INSERT INTO users (login, email, password, token) VALUES (:login, :email, :password, :token)");
        $stmt->execute([
            ':login' => $this->login,
            ':email' => $this->email,
            ':password' => password_hash($this->password, PASSWORD_BCRYPT),
            ':token' => $this->token
        ]);
        $this->id = $pdo->lastInsertId();
    }

    /**
     * Summary of Update permet de mettre à jour un user dans la base de données
     * @return void
     */
    public function Update() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("UPDATE users SET login = :login, email = :email, password = :password, token = :token WHERE id = :id");
        $stmt->execute([
            ':id' => $this->id,
            ':login' => $this->login,
            ':email' => $this->email,
            ':password' => password_hash($this->password, PASSWORD_BCRYPT),
            ':token' => $this->token
        ]);
    }

    /**
     * Summary of Delete permet de supprimer un user dans la base de données
     * @return void
     */
    public function Delete() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
    }

    /**
     * Summary of ReadAll permet de lire tous les users dans la base de données
     * @return array
     */

    public static function ReadAll() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Summary of ReadById permet de lire un user dans la base de données en fonction de son id
     * @param int $id
     * @return array
     */
    public static function ReadById($id) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Summary of ReadByEmail permet de lire un user dans la base de données en fonction de son email
     * @param string $email
     * @return array
     */
    public static function ReadByEmail($email) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}