<?php
namespace App\Controllers;

use App\Models\PDOSingleton;
use App\Models\Annonce;
use PDO;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AnnonceController
{
    private string $secret = "8f3c9c2b7a1d4e6f9c0b5a7d9e1f2c3a_super_secret_key_2026";
    /**
     * Summary of recupererAnnonces permet de faire des filtres de recherches
     * @param Request $request
     * @param Response $response
     */
    public function recupererAnnonces(Request $request, Response $response)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        // Récupérer les paramètres de recherche depuis la requête GET
        $title = $_GET['title'] ?? null;
        $description = $_GET['description'] ?? null;
        $sale = $_GET['sale'] ?? null;
        $location = $_GET['location'] ?? null;
        $brand = $_GET['brand'] ?? null;
        $model = $_GET['model'] ?? null;
        $price_min = $_GET['price_min'] ?? null;
        $price_max = $_GET['price_max'] ?? null;
        $year_min = $_GET['year_min'] ?? null;
        $year_max = $_GET['year_max'] ?? null;

        // Protection contre injectin SQL
        $sort_by = $_GET['sort_by'] ?? 'date_publication';
        $sort_order = $_GET['sort_order'] ?? 'DESC';

        $allowed_sort = ['price', 'year_first_registration', 'date_publication'];
        $allowed_order = ['ASC', 'DESC'];
        //
        if (!in_array($sort_by, $allowed_sort))
            $sort_by = 'date_publication';
        if (!in_array(strtoupper($sort_order), $allowed_order))
            $sort_order = 'DESC';

        //Requete sql pour les filtres
        $sql = "SELECT * FROM advertisements  WHERE 1=1";
        $params = [];

        if ($title) {
            $sql .= " AND title LIKE :title";
            $params[':title'] = "%$title%";
        }
        if ($description) {
            $sql .= " AND description LIKE :description";
            $params[':description'] = "%$description%";
        }
        if (!is_null($sale) && $sale !== '') {
            $sql .= " AND sale = :sale";
            $params[':sale'] = (int) $sale;
        }
        if ($location) {
            $sql .= " AND location LIKE :location";
            $params[':location'] = "%$location%";
        }
        if ($brand) {
            $sql .= " AND brand LIKE :brand";
            $params[':brand'] = "%$brand%";
        }
        if ($model) {
            $sql .= " AND model LIKE :model";
            $params[':model'] = "%$model%";
        }

        if ($price_min) {
            $sql .= " AND price >= :price_min";
            $params[':price_min'] = (float) $price_min;
        }
        if ($price_max) {
            $sql .= " AND price <= :price_max";
            $params[':price_max'] = (float) $price_max;
        }
        if ($year_min) {
            $sql .= " AND year_first_registration >= :year_min";
            $params[':year_min'] = (int) $year_min;
        }
        if ($year_max) {
            $sql .= " AND year_first_registration <= :year_max";
            $params[':year_max'] = (int) $year_max;
        }


        $sql .= " ORDER BY $sort_by $sort_order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($annonces));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    /**
     * Summary of getUtilisateurConnecte permet de récuperer l'utilisateur à partir du JWT
     * @param Request $request
     * @return array|null
     */
    private function getUtilisateurConnecte(Request $request)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = substr($authHeader, 7);
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return User::readById($decoded->id);
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Summary of recupererAnnooncesById permet de récuperer une annonce par son id
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function recupererAnnooncesById(Request $request, Response $response, array $args)
    {
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['message' => 'Annonce non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } else {
            $response->getBody()->write(json_encode($annonce));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
    }
    /**
     * Summary of creerAnnonce permet de créer une annonce
     * @param Request $request
     * @param Response $response
     */
    public function creerAnnonce(Request $request, Response $response)
    {
        $user = $this->getUtilisateurConnecte($request);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Pas autorisé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $data = json_decode($request->getBody()->getContents(), true);
        if (empty($data['title'])) {
            $response->getBody()->write(json_encode(['error' => 'Le titre est requis']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        $annonce = new Annonce(
            null,
            $data['title'],
            $data['description'] ?? null,
            isset($data['sale']) ? (int) $data['sale'] : null,
            $data['location'] ?? null,
            $data['brand'] ?? null,
            $data['model'] ?? null,
            isset($data['price']) ? (float) $data['price'] : null,
            isset($data['year_first_registration']) ? (int) $data['year_first_registration'] : null,
            date('Y-m-d H:i:s'),
            $user['id_user']
        );
        $annonce->create();
        $response->getBody()->write(json_encode([
            'message' => 'annonce créee',
            'id_advertisement' => $annonce->id_advertisement
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    /**
     * Summary of modifierAnnonce permet de modifier une annonce
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function modifierAnnonce(Request $request, Response $response, array $args)
    {
        $user = $this->getUtilisateurConnecte($request);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Non autorisé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        //Vérifie si l'annonce existe
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['error' => 'Annonce non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        //Vérifie le propriétaire de l'annonce
        if ((int) $annonce['id_user'] !== (int) $user['id_user']) {
            $response->getBody()->write(json_encode(['error' => 'Accès interdit']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $data = json_decode($request->getBody()->getContents(), true);

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $annonce)) {
                $annonce[$key] = $value;
            }
        }

        $updatedAnnonce = new Annonce(
            $annonce['id_advertisement'],
            $annonce['title'],
            $annonce['description'],
            (int) $annonce['sale'],
            $annonce['location'],
            $annonce['brand'],
            $annonce['model'],
            (float) $annonce['price'],
            (int) $annonce['year_first_registration'],
            $annonce['date_publication'],
            (int) $annonce['id_user']
        );
        $updatedAnnonce->update();

        $response->getBody()->write(json_encode(['message' => 'Annonce modifiée avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    /**
     * Summary of supprimerAnnonce permet de supprimer une annonce
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function supprimerAnnonce(Request $request, Response $response, array $args)
    {
        //Vérifie l'utilisateur connecté
        $user = $this->getUtilisateurConnecte($request);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Pas autorisé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        //Vérifie si l'annonce existe 
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['error' => 'Annonce non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        //Vérifie le propriétaire de l'annonce
        if ((int) $annonce['id_user'] !== (int) $user['id_user']) {
            $response->getBody()->write(json_encode(['error' => 'Accès interdit']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $annonceObjet = new Annonce($args['id'], null, null, null, null, null, null, null, null, null, null);
        $annonceObjet->delete();

        $response->getBody()->write(json_encode(['message' => 'Annonce supprimée']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}