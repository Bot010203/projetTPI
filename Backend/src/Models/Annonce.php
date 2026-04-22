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

class Annonce {
    public $id;
    public $title;
    public $description;
    //boolean pour savoir si l'annonce est à vendre ou à acheter
    public $sale;
    public $location;
    public $brand;
    public $model;
    public $price;
    public $year_of_first_registration;
    public $date_of_publication;                                                                                                                                                                                                         
    public $user_id;

    public function __construct($id, $title, $description, $sale, $location, $brand, $model, $price, $year_of_first_registration, $date_of_publication, $user_id) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->sale = $sale;
        $this->location = $location;
        $this->brand = $brand;
        $this->model = $model;
        $this->price = $price;
        $this->year_of_first_registration = $year_of_first_registration;
        $this->date_of_publication = $date_of_publication;
        $this->user_id = $user_id;
    }

    /**
     * Summary of Create permet de créer une annonce dans la base de données
     * @return void
     */
    public function Create() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("INSERT INTO annonces (title, description, sale, location, brand, model, price, year_of_first_registration, date_of_publication, user_id) VALUES (:title, :description, :sale, :location, :brand, :model, :price, :year_of_first_registration, :date_of_publication, :user_id)");
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':sale' => (int)$this->sale,
            ':location' => $this->location,
            ':brand' => $this->brand,
            ':model' => $this->model,
            ':price' => $this->price,
            ':year_of_first_registration' => $this->year_of_first_registration,
            ':date_of_publication' => $this->date_of_publication,
            ':user_id' => $this->user_id
        ]);
        $this->id = $pdo->lastInsertId();
    }
    /**
     * Summary of Update permet de mettre à jour une annonce dans la base de données
     * @return void
     */
    public function Update() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("UPDATE annonces SET title = :title, description = :description, sale = :sale, location = :location, brand = :brand, model = :model, price = :price, year_of_first_registration = :year_of_first_registration, date_of_publication = :date_of_publication, user_id = :user_id WHERE id = :id");
        $stmt->execute([
            ':id' => $this->id,
            ':title' => $this->title,
            ':description' => $this->description,
            ':sale' => (int)$this->sale,
            ':location' => $this->location,
            ':brand' => $this->brand,
            ':model' => $this->model,
            ':price' => $this->price,
            ':year_of_first_registration' => $this->year_of_first_registration,
            ':date_of_publication' => $this->date_of_publication,
            ':user_id' => $this->user_id
        ]);
    }
    /**
     * Summary of Delete permet de supprimer une annonce dans la base de données
     * @return void
     */
    public function Delete() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("DELETE FROM annonces WHERE id = :id");
        $stmt->execute([':id' => $this->id]);
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
     * @param int $id
      * @return array
      */
    public static function ReadById($id) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM annonces WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Summary of ReadByUserId permet de lire les annonces d'un user par son id dans la base de données
     * @param int $user_id
      * @return array
      */
    public static function ReadByUserId($user_id) {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM annonces WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);                                                   
    }
}                                                                                                                                                                                                                                                           
                                                                                                                                                                                                                                                           