<?php
namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use PDO;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AnnonceController
{
    public function recupererAnnonces(Request $request, Response $response)
    {
        $pdo=PDO::getInstance()->getConnection();
        // Récupérer les paramètres de recherche depuis la requête GET
        if(isset($_GET['title'])){
            $title=$_GET['title'];
        }else{
            $title=null;
        }
        if(isset($_GET['description'])){
            $description=$_GET['description'];
        }else{
            $description=null;
        }
        if(isset($_GET['sale'])){
            $sale=$_GET['sale'];
        }else{
            $sale=null;
        }
        if(isset($_GET['location'])){
            $location=$_GET['location'];
        }else{
            $location=null;
        }
        if(isset($_GET['brand'])){
            $brand=$_GET['brand'];
        }else{
            $brand=null;
        }
        if(isset($_GET['model'])){
            $model=$_GET['model'];
        }else{
            $model=null;
        }
        if(isset($_GET['price_min'])){
            $price_min=$_GET['price_min'];
        }else{
            $price_min=null;
        }
        if(isset($_GET['price_max'])){
            $price_max=$_GET['price_max'];
        }else{
            $price_max=null;
        }
        if(isset($_GET['year_min'])){
            $year_min=$_GET['year_min'];
        }else{
            $year_min=null;
        }
        if(isset($_GET['year_max'])){
            $year_max=$_GET['year_max'];
        }else{
            $year_max=null;
        }

        //Filtrer les annonces du plus récent au plus vieux
        if(isset($_GET['sort_by'])){
            $sort_by=$_GET['sort_by'];
        }else{
            $sort_by=null;
        }
        if(isset($_GET['sort_order'])){
            $sort_order=$_GET['sort_order'];
        }else{
            $sort_order=null;
        }
        

        if(!sort($sort_by)){
            $sort_by='date_publication';
        }
        if(!sort($sort_order)){
            $sort_order='DESC';
        }
        if (isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
        } else {
            $sort_by = 'date_publication';
        }

        if (isset($_GET['sort_order'])) {
            $sort_order = $_GET['sort_order'];
        } else {
            $sort_order = 'DESC';
        }
        //Requete sql pour les filtres
        $sql = "SELECT * FROM annonces WHERE 1=1";
        $params = [];

        if($title){
            $sql .= " AND title LIKE :title";
            $params[':title'] = "%$title%";
        }
        if($description){
            $sql .= " AND description LIKE :description";
            $params[':description'] = "%$description%";
        }
        if($sale){
            $sql .= " AND sale = :sale";
            $params[':sale'] = $sale;
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
        if($price_min){
            $sql .= " AND price >= :price_min";
            $params[':price_min'] = $price_min;
        }
        if($price_max){
            $sql .= " AND price <= :price_max";
            $params[':price_max'] = $price_max;
        }
        if($year_min){
            $sql .= " AND year_first_registration >= :year_min";
            $params[':year_min'] = $year_min;
        }
        if($year_max){
            $sql .= " AND year_first_registration <= :year_max";
            $params[':year_max'] = $year_max;
        }


        $sql .= " ORDER BY $sort_by $sort_order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $response->withJson($annonces);
    }
}