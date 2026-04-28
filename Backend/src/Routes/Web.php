<?php
namespace App\Routes;
use App\Controllers\AuthController;
use App\Controllers\AnnonceController;
use Slim\App;
final class Web {
    /**
     * Summary of register 
     * @param App $app
     * @return void
     */
    public static function register(App $app): void {
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
    }
}

