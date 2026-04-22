<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : Annonce.php
 * But : Classe Annonce avec CRUD pour gérer les annonces du site web
 */
namespace App\Models;

use App\Models\PDOSingleton;
use PDO;
use App\Models\User;
use App\Models\Message;

class Annonce {
    public $id_advertisement;
    public $title;
    public $description;
    //boolean pour savoir si l'annonce est à vendre ou à acheter
    public $sale;
    public $location;
    public $brand;
    public $model;
    public $price;
    public $year_first_registration;
    public $date_publication;                                                                                                                                                                                                         
    public $id_user;

    public function __construct($id_advertisement, $title, $description, $sale, $location, $brand, $model, $price, $year_first_registration, $date_publication, $id_user) {
        $this->id_advertisement = $id_advertisement;
        $this->title = $title;
        $this->description = $description;
        $this->sale = $sale;
        $this->location = $location;
        $this->brand = $brand;
        $this->model = $model;
        $this->price = $price;
        $this->year_first_registration = $year_first_registration;
        $this->date_publication = $date_publication;
        $this->id_user = $id_user;
    }

    /**
     * Summary of Create permet de créer une annonce dans la base de données
     * @return void
     */
    public function Create() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("INSERT INTO annonces (title, description, sale, location, brand, model, price, year_first_registration, date_publication, id_user) VALUES (:title, :description, :sale, :location, :brand, :model, :price, :year_first_registration, :date_publication, :id_user)");
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':sale' => (int)$this->sale,
            ':location' => $this->location,
            ':brand' => $this->brand,
            ':model' => $this->model,
            ':price' => $this->price,
            ':year_first_registration' => $this->year_first_registration,
            ':date_publication' => $this->date_publication,
            ':id_user' => $this->id_user
        ]);
        $this->id_advertisement = $pdo->lastInsertId();
    }

    /**
     * Summary of Update permet de mettre à jour une annonce dans la base de données
     * @return void
     */
    public function Update() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("UPDATE annonces SET title = :title, description = :description, sale = :sale, location = :location, brand = :brand, model = :model, price = :price, year_first_registration = :year_first_registration, date_publication = :date_publication, id_user = :id_user WHERE id_advertisement = :id_advertisement");
        $stmt->execute([
            ':id_advertisement' => $this->id_advertisement,
            ':title' => $this->title,
            ':description' => $this->description,
            ':sale' => (int)$this->sale,
            ':location' => $this->location,
            ':brand' => $this->brand,
            ':model' => $this->model,
            ':price' => $this->price,
            ':year_first_registration' => $this->year_first_registration,
            ':date_publication' => $this->date_publication,
            ':id_user' => $this->id_user
        ]);
    }

    /**
     * Summary of Delete permet de supprimer une annonce dans la base de données
     * @return void
     */
    public function Delete() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("DELETE FROM annonces WHERE id_advertisement = :id_advertisement");
        $stmt->execute([':id_advertisement' => $this->id_advertisement]);
    }

    /**
     * Summary of ReadAll permet de lire toutes les annonces dans la base de données
     * @return array
     */
    public static function ReadAll() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM annonces");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of ReadById permet de lire une annonce par son id dans la base de données
     * @param int $id_advertisement
     * @return array
     */
    public static function ReadById($id_advertisement) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id_advertisement = :id_advertisement");
        $stmt->execute([':id_advertisement' => $id_advertisement]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Summary of ReadByUserId permet de lire les annonces d'un user par son id dans la base de données
     * @param int $id_user
     * @return array
     */
    public static function ReadByUserId($id_user) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id_user = :id_user");
        $stmt->execute([':id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);                                                   
    }
}