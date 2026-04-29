<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : AuthMiddleware.php
 * But : Middleware pour vérifier le token JWT
 */
namespace App\Middleware;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
    private string $secret = "8f3c9c2b7a1d4e6f9c0b5a7d9e1f2c3a_super_secret_key_2026";

    public function __invoke(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized();
        }

        $token = substr($authHeader, 7);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $user    = User::readById($decoded->id);

            if (!$user) {
                return $this->unauthorized();
            }

            // Injecte l'utilisateur dans la requête pour l'utiliser dans les controllers
            $request = $request->withAttribute('user', $user);
            return $handler->handle($request);

        } catch (\Exception $e) {
            return $this->unauthorized();
        }
    }

    private function unauthorized(): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => 'Non autorisé']));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}