<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : image.php
 * But : Classe Image pour gérer les images des annonces du site web
 */
namespace App\Models;
use App\Models\PDOSingleton;
use PDO;
/**
 * Summary of Image classe pour gérer les images des annonces
 */
class Image
{
    public $id_picture;
    public $path;
    public $id_advertisement;

    public function __construct($id_picture, $path, $id_advertisement)
    {
        $this->id_picture = $id_picture;
        $this->path = $path;
        $this->id_advertisement = $id_advertisement;
    }
    /**
     * Summary of ajouterImage permet d'ajouter une image
     * @return void
     */
    public function ajouterImage()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO Picture (path, id_advertisement) VALUES (:path, :id_advertisement)");
        $stmt->execute([
            ':path' => $this->path,
            ':id_advertisement' => $this->id_advertisement
        ]);
        $this->id_picture = $pdo->lastInsertId();
    }
    /**
     * Summary of supprimerImage permet de supprimer une image
     * @return void
     */
    public function supprimerImage()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM Picture WHERE id_picture = :id_picture");
        $stmt->execute([
            ':id_picture' => $this->id_picture
        ]);
    }
    /**
     * Summary of recupererImagesParAnnonce permet de récupérer les images d'une annonce
     * @param int $id_advertisement
     * @return array
     */
    public static function readByAdvertisementId($id_advertisement)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Picture WHERE id_advertisement = :id_advertisement");
        $stmt->execute([
            ':id_advertisement' => $id_advertisement
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Summary of recupererImageParId permet de récupérer une image par son id
     * @param int $id_picture
     * @return array
     */
    public static function readById($id_picture)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Picture WHERE id_picture = :id_picture");
        $stmt->execute([
            ':id_picture' => $id_picture
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}