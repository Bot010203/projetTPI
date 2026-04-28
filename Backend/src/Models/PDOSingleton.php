<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : PDOSingleton.php
 * But : Classe PDOSingleton pour gérer la connexion à la base de données
 */
namespace App\Models;
use PDO;
use PDOException;

/**
 * Summary of PDOSingleton classe pour la connexion a la bdd
 */
class PDOSingleton
{
    // Stocke l'instance unique de la classe
    private static $instance=null;
    //Connexion à la PDO
    private $pdo;
    // Informations de connexion à la base de données
    private $host='localhost';
    private $db='annonces_vehicules_db';
    private $user='root';
    private $pass='xgfmn3';
    private $charset = 'utf8mb4';
    private function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options=[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Gérer les erreurs avec des exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Résultats sous forme de tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES => false, // Désactiver l'émulation des requêtes préparées
        ];
        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    private function __clone() {}
    // Empêche la désérialisation
    public function __wakeup() {}
    // Mé thode pour obtenir l'instance unique ( Singleton )
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    // Récupère l'objet PDO pour exé cuter des requ êtes SQL
    public function getConnection()
    {
        return $this->pdo;
    }

}