<?php
namespace App\Routes;
use App\Controllers\AuthController;
use App\Controllers\AnnonceController;
use App\Controllers\ImageController;
use Slim\App;
final class Web
{
    /**
     * Summary of register 
     * @param App $app
     * @return void
     */
    public static function register(App $app): void
    {
        $app->get('/', function ($request, $response) {
            $response->getBody()->write('Bienvenue');
            return $response;
        });
        // Routes d'authentification
        $app->post('/login', [AuthController::class, 'login']);
        $app->post('/register', [AuthController::class, 'register']);

        // Routes pour les annonces
        $app->get('/annonces', [AnnonceController::class, 'recupererAnnonces']);
        $app->get('/annonces/{id}', [AnnonceController::class, 'recupererAnnooncesById']);
        $app->post('/annonces', [AnnonceController::class, 'creerAnnonce']);
        $app->put('/annonces/{id}', [AnnonceController::class, 'modifierAnnonce']);
        $app->delete('/annonces/{id}', [AnnonceController::class, 'supprimerAnnonce']);

        // Routes pour les images
        $app->post('/annonces/{id}/images', [ImageController::class, 'ajouterImage']);
        $app->get('/annonces/{id}/images', [ImageController::class, 'recupererImages']);
        $app->delete('/annonces/{id}/images/{id_image}', [ImageController::class, 'supprimerImage']);

        // Routes pour les messages
        $app->post('/annonces/{id}/messages', [MessageController::class, 'envoyerMessage']);
        $app->get('/conversations', [MessageController::class, 'avoirConversations']);
        $app->get('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'avoirMessagesParConversation']);
        $app->delete('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'supprimerConversation']);
    }
}

