<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : AuthController.php
 * But : Classe AuthController pour gérer les opérations d'authentification
 */

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController
{
    private string $secret = "8f3c9c2b7a1d4e6f9c0b5a7d9e1f2c3a_super_secret_key_2026"; 

    public function login(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true) ?? [];
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';    

        if ($email == '' || $password == '') {
            return $this->send($response, "Email et mot de passe requis", 400);
        }

        $user = User::readByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->send($response, "Email ou mot de passe incorrect", 401);
        }

        $token = JWT::encode(
            [
                'id' => $user['id_user'],
                'email' => $user['email'],
                'exp' => time() + 3600
            ],
            $this->secret,
            'HS256'
        );

        $userObj = new User($user['id_user'], $user['login'], $user['email'], $user['password'], $token);
        $userObj->updateToken();

        return $this->send($response, ['token' => $token]);
    }

    public function register(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody()->getContents(), true) ?? [];

        $login = $data['login'] ?? '';
        $email  = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if ($login == '' || $email == '' || $password == '') {
            return $this->send($response, "Login, email et mot de passe requis", 400);
        }

        // Vérifie le format de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->send($response, "Format de l'email invalide", 422);
        }

        // Vérifie la confirmation du mot de passe
        if ($password !== $confirmPassword) {
            return $this->send($response, "Les mots de passe ne correspondent pas", 422);
        }

        if (User::readByEmail($email)) {
            return $this->send($response, "Email déjà utilisé", 409);
        }

        if (User::readByLogin($login)) {
            return $this->send($response, "Login déjà utilisé", 409);
        }

        $user = new User(
            null,
            $login,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            null
        );

        $user->create();

        return $this->send($response, [
            'message' => 'Utilisateur créé avec succès'
        ], 201);
    }

    /**
     * Summary of send permet d'envoyer la réponse json à l'api
     * @param Response $response
     * @param mixed $data
     * @param mixed $status
     * @return Response
     */
    private function send(Response $response, $data, $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}