<?php
namespace App\Controllers;

use App\Models\PDOSingleton;
use App\Models\User;
use Firebase\JWT\JWT;
use PDO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AnnonceController
{
    public function recupererAnnonces(Request $request, Response $response)
    {
        $pdo=PDOSingleton::getInstance()->getConnection();
        // Récupérer les paramètres de recherche depuis la requête GET
        $title       = $_GET['title']       ?? null;
        $description = $_GET['description'] ?? null;
        $sale        = $_GET['sale']        ?? null;
        $location    = $_GET['location']    ?? null;
        $brand       = $_GET['brand']       ?? null;
        $model       = $_GET['model']       ?? null;
        $price_min   = $_GET['price_min']   ?? null;
        $price_max   = $_GET['price_max']   ?? null;
        $year_min    = $_GET['year_min']    ?? null;
        $year_max    = $_GET['year_max']    ?? null;

        // Protection contre injectin SQL
        $sort_by    = $_GET['sort_by']    ?? 'date_publication';
        $sort_order = $_GET['sort_order'] ?? 'DESC';

        $allowed_sort  = ['price', 'year_first_registration', 'date_publication'];
        $allowed_order = ['ASC', 'DESC'];
        //
        if (!in_array($sort_by, $allowed_sort))                 $sort_by    = 'date_publication';
        if (!in_array(strtoupper($sort_order), $allowed_order)) $sort_order = 'DESC';
        
        //Requete sql pour les filtres
        $sql = "SELECT * FROM advertisements  WHERE 1=1";
        $params = [];

        if($title){
            $sql .= " AND title LIKE :title";
            $params[':title'] = "%$title%";
        }
        if($description){
            $sql .= " AND description LIKE :description";
            $params[':description'] = "%$description%";
        }
       if (!is_null($sale) && $sale !== '') {
            $sql .= " AND sale = :sale";
            $params[':sale'] = (int)$sale;
        }
         if($location){
            $sql .= " AND location LIKE :location";
            $params[':location'] = "%$location%";
        }
        if($brand){
            $sql .= " AND brand LIKE :brand";
            $params[':brand'] = "%$brand%";
        }
        if($model){
            $sql .= " AND model LIKE :model";
            $params[':model'] = "%$model%";
        }

       if ($price_min) {
            $sql .= " AND price >= :price_min";
            $params[':price_min'] = (float)$price_min;
        }
        if ($price_max) {
            $sql .= " AND price <= :price_max";
            $params[':price_max'] = (float)$price_max;
        }
        if ($year_min) {
            $sql .= " AND year_first_registration >= :year_min";
            $params[':year_min'] = (int)$year_min;
        }
        if ($year_max) {
            $sql .= " AND year_first_registration <= :year_max";
            $params[':year_max'] = (int)$year_max;
        }


        $sql .= " ORDER BY $sort_by $sort_order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($annonces));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}