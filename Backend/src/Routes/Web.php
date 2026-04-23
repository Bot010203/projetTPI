<?php
namespace App\Routes;
use App\Controllers\AuthController;
use Slim\App;
final class Web {
    public static function register(App $app): void {
        $app->get('/', function ($request, $response) {
            $response->getBody()->write('Bienvenu');
            return $response;
        });
        $app->post('/login', [AuthController::class, 'login']);
        $app->post('/register', [AuthController::class, 'register']);
        
    }
}

