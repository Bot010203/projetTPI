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
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
/**
 * Classe ImageController
 */
class ImageController
{

    /**
     * Permet d'ajouter une image
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function addImage(Request $request, Response $response, array $args): Response
    {
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $typesAutorises = ['image/jpeg', 'image/png', 'image/gif'];
        $tailleMax = 5 * 1024 * 1024;
        $user = $request->getAttribute('user');

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

        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles['image'])) {
            $response->getBody()->write(json_encode(['error' => 'Aucune image envoyée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

        $file = $uploadedFiles['image'];

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

        if ($file->getSize() > $tailleMax) {
            $response->getBody()->write(json_encode(['error' => 'Fichier trop volumineux (max 5MB)']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
        }

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
        $image->addImage();

        $response->getBody()->write(json_encode([
            'message' => 'Image uploadée avec succès',
            'id_picture' => $image->id_picture,
            'path' => $image->path
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    /**
     * permet de récupérer les images d'une annonce
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getImages(Request $request, Response $response, array $args)
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
    /**
     * permet de supprimer une image
     * @param Request $request
     * @param Response $response
     * @param array $args
     */

    public function deleteImage(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');

        $image = Image::readById($args['id']);
        if (!$image) {
            $response->getBody()->write(json_encode(['error' => 'Image non trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $annonce = Annonce::readById($image['id_advertisement']);
        if ((int) $annonce['id_user'] !== (int) $user['id_user']) {
            $response->getBody()->write(json_encode(['error' => 'Accès interdit']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $filePath = __DIR__ . '/../../public' . $image['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $imageObj = new Image($args['id'], null, null);
        $imageObj->deleteImage();

        $response->getBody()->write(json_encode(['message' => 'Image supprimée avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
