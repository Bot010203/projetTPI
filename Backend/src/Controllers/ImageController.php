<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : image.php
 * But : Classe Image pour gérer les images des annonces du site web
 */
namespace App\Controllers;
use App\Models\Image;
use App\Models\Annonce;
use App\Controllers\AnnonceController;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ImageController
{


    public function ajouterImage(Request $request, Response $response, array $args): Response
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $typesAutorises = ['image/jpeg', 'image/png', 'image/gif'];
        $tailleMax = 5 * 1024 * 1024;
        //Vérifier que l'utilisateur est connecté
        $user = $this->getUtilisateurConnecte($request);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Non autorisé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        //Vérifier que l'annonce existe
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['error' => 'Annonce non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Vérifier que l'annonce appartient à l'utilisateur connecté
        if ((int) $annonce['id_user'] !== (int) $user['id_user']) {
            $response->getBody()->write(json_encode(['error' => 'Accès interdit']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        //Vérifier qu'un fichier a bien été envoyé
        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles['image'])) {
            $response->getBody()->write(json_encode(['error' => 'Aucune image envoyée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        $file = $uploadedFiles['image'];

        //Vérifier qu'il n'y a pas eu d'erreur lors de l'upload
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de l\'upload']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        //Vérifier le type MIME réel du fichier
        $tmpPath = $file->getStream()->getMetadata('uri');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mimeType, $typesAutorises)) {
            $response->getBody()->write(json_encode(['error' => 'Type de fichier non autorisé (jpeg, png, webp uniquement)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        //Vérifier la taille du fichier
        if ($file->getSize() > $tailleMax) {
            $response->getBody()->write(json_encode(['error' => 'Fichier trop volumineux (max 5MB)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        //Créer le dossier d'upload si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        //Générer un nom unique pour éviter les collisions
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => 'jpg'
        };
        $filename = uniqid('img_' . $args['id'] . '_', true) . '.' . $extension;
        $destPath = $uploadDir . $filename;

        //Déplacer le fichier vers le dossier d'upload
        $file->moveTo($destPath);

        //Sauvegarder le chemin en base de données
        $image = new Image(null, '/uploads/' . $filename, $args['id']);
        $image->ajouterImage();

        $response->getBody()->write(json_encode([
            'message' => 'Image uploadée avec succès',
            'id_picture' => $image->id_picture,
            'path' => $image->path
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function recupererImages(Request $request, Response $response, array $args)
    {
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['error' => 'Annonce non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $images = Image::readByAdvertisementId($args['id']);

        $response->getBody()->write(json_encode($images));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function supprimerImage(Request $request, Response $response, array $args)
    {
        // Vérifier que l'utilisateur est connecté
        $user = $this->getUtilisateurConnecte($request);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Non autorisé']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Vérifier que l'image existe
        $image = Image::readById($args['id']);
        if (!$image) {
            $response->getBody()->write(json_encode(['error' => 'Image non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // Vérifier que l'annonce associée appartient à l'utilisateur connecté
        $annonce = Annonce::readById($image['id_advertisement']);
        if ((int) $annonce['id_user'] !== (int) $user['id_user']) {
            $response->getBody()->write(json_encode(['error' => 'Accès interdit']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        // Supprimer le fichier physique
        $filePath = __DIR__ . '/../../public' . $image['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Supprimer l'image de la base de données
        $imageObj = new Image($args['id'], null, null);
        $imageObj->supprimerImage();

        $response->getBody()->write(json_encode(['message' => 'Image supprimée avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    private function getUtilisateurConnecte(Request $request)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        try {
            $secret = "8f3c9c2b7a1d4e6f9c0b5a7d9e1f2c3a_super_secret_key_2026";
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return User::readById($decoded->id);
        } catch (\Exception $e) {
            return null;
        }
    }

}
