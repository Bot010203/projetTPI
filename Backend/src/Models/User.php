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
    public $id_user;
    public $email;
    public $login;
    public $password;
    public $token;

    public function __construct($id_user, $login, $email, $password, $token) {
        $this->id_user = $id_user;
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }

    /**
     * Summary of Create permet de créer un user dans la base de données
     * @return void
     */
    public function create() {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO users (login, email, password, token) VALUES (:login, :email, :password, :token)");
        $stmt->execute([
            ':login' => $this->login,
            ':email' => $this->email,
            ':password' => $this->password,
            ':token' => $this->token
        ]);
        $this->id_user = $pdo->lastInsertId();
    }

    /**
     * Summary of Update permet de mettre à jour un user dans la base de données
     * @return void
     */
    public function update() {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE users SET login = :login, email = :email, password = :password, token = :token WHERE id_user = :id_user");
        $stmt->execute([
            ':id_user' => $this->id_user,
            ':login' => $this->login,
            ':email' => $this->email,
            ':password' => password_hash($this->password, PASSWORD_BCRYPT),
            ':token' => $this->token
        ]);
    }

    /**
     * Met à jour uniquement le token (A14 : appelé à chaque login)
     * @return void
     */
    public function updateToken() {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE users SET token = :token WHERE id_user = :id_user");
        $stmt->execute([
            ':token' => $this->token,
            ':id_user' => $this->id_user
        ]);
    }

    /**
     * Summary of Delete permet de supprimer un user dans la base de données
     * @return void
     */
    public function delete() {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = :id_user");
        $stmt->execute([':id_user' => $this->id_user]);
    }

    /**
     * Summary of ReadAll permet de lire tous les users dans la base de données
     * @return array
     */
    public static function readAll() {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of ReadById permet de lire un user dans la base de données en fonction de son id
     * @param int $id
     * @return array
     */
    public static function readById($id) {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of ReadByEmail permet de lire un user dans la base de données en fonction de son email
     * @param string $email
     * @return array
     */
    public static function readByEmail($email) {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of ReadByLogin permet de lire un user dans la base de données en fonction de son login
     * @param string $login
     * @return array
     */
    public static function readByLogin($login) {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->execute([':login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}