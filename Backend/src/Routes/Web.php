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


        $app->post('/login', [AuthController::class, 'login']);
        $app->post('/register', [AuthController::class, 'register']);

        $app->get('/annonces', [AnnonceController::class, 'recupererAnnonces']);
        $app->get('/annonces/{id}', [AnnonceController::class, 'recupererAnnooncesById']);

        $app->get('/annonces/{id}/images', [ImageController::class, 'recupererImages']);

        $app->group('', function ($group) {

            // Annonces
            $group->get('/mes-annonces', [AnnonceController::class, 'mesAnnonces']);
            $group->post('/annonces', [AnnonceController::class, 'creerAnnonce']);
            $group->put('/annonces/{id}', [AnnonceController::class, 'modifierAnnonce']);
            $group->delete('/annonces/{id}', [AnnonceController::class, 'supprimerAnnonce']);



            // Images
            $group->post('/annonces/{id}/images', [ImageController::class, 'ajouterImage']);
            $group->delete('/annonces/{id}/images/{id_image}', [ImageController::class, 'supprimerImage']);

            // Messages
            $group->post('/annonces/{id}/messages', [MessageController::class, 'envoyerMessage']);
            $group->get('/conversations', [MessageController::class, 'avoirConversations']);
            $group->get('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'avoirMessagesParConversation']);
            $group->delete('/conversations/{id_advertisement}/{id_user}', [MessageController::class, 'supprimerConversation']);

        })->add(new AuthMiddleware());
    }
}

