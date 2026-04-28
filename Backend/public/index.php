<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : index.php
 * But : Point d'entrée de l'application
 */
use Slim\Factory\AppFactory;
use App\Routes\Web;


require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// CORS (premier)
$app->add(function ($request, $handler) {
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$app->addBodyParsingMiddleware();

Web::register($app);

$app->run();
