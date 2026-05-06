<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : Web.php
 * But : Classe Web avec les méthodes pour gérer les routes du site web
 */
namespace App\Routes;
use App\Controllers\AuthController;
use App\Controllers\AnnonceController;
use App\Controllers\ImageController;
use App\Controllers\MessageController;
use App\Middleware\AuthMiddleware;
use Slim\App;
final class Web
{
    public static function register(App $app): void
    {
        $app->get('/', function ($request, $response) {
            $response->getBody()->write('Bienvenue');
            return $response;
        });

        $app->post('/login', [AuthController::class, 'login']);
        $app->post('/register', [AuthController::class, 'register']);

        $app->get('/annonces', [AnnonceController::class, 'getAdvertisements']);
        $app->get('/annonces/{id}', [AnnonceController::class, 'getAdvertisementById']);

        $app->get('/annonces/{id}/images', [ImageController::class, 'getImages']);

        $app->group('', function ($group) {

            // Annonces
            $group->get('/mes-annonces', [AnnonceController::class, 'getMyAdvertisements']);
            $group->post('/annonces', [AnnonceController::class, 'createAdvertisement']);
            $group->put('/annonces/{id}', [AnnonceController::class, 'updateAdvertisement']);
            $group->delete('/annonces/{id}', [AnnonceController::class, 'deleteAdvertisement']);

            // Images
            $group->post('/annonces/{id}/images', [ImageController::class, 'addImage']);
            $group->delete('/annonces/{id}/images/{id_image}', [ImageController::class, 'deleteImage']);

            // Messages
            $group->post('/annonces/{id}/messages', [MessageController::class, 'sendMessage']);
            $group->get('/conversations', [MessageController::class, 'getConversations']);
            $group->get('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'getMessagesByConversation']);
            $group->delete('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'deleteConversation']);

        })->add(new AuthMiddleware());
    }
}

