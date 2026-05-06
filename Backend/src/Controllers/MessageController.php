<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : MessageController.php
 * But : Classe MessageController avec les méthodes pour gérer les messages du site web
 */
namespace App\Controllers;
use App\Models\Message;
use App\Models\Annonce;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
/**
 * Summary of MessageController
 */
class MessageController
{
    /**
     * Summary of envoyerMessage permet d'envyoer un message
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function envoyerMessage(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $annonce = Annonce::readById($args['id']);
        if (!$annonce) {
            $response->getBody()->write(json_encode(['error' => 'Annonce pas trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = json_decode($request->getBody()->getContents(), true);
        if (empty($data['text'])) {
            $response->getBody()->write(json_encode(['error' => 'Champ text obligatoire']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        if ((int) $user['id_user'] === (int) $annonce['id_user']) {
            $id_recipient = (int) ($data['id_recipient'] ?? 0);
            if (!$id_recipient) {
                $response->getBody()->write(json_encode(['error' => 'Destinataire manquant']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(422);
            }
        } else {
            $id_recipient = (int) $annonce['id_user'];
        }
        $message = new Message(
            null,
            $data['text'],
            date('Y-m-d H:i:s'),
            false,
            $user['id_user'],
            $id_recipient,  
            $args['id'],
            $data['original_message_id'] ?? null
        );
        $message->create();
        $response->getBody()->write(json_encode(['message' => 'message envoyé, id_message: ' . $message->id_message]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    /**
     * Summary of avoirConversations permet de récuperer la conversation
     * @param Request $request
     * @param Response $response
     */
    public function avoirConversations(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        $conversations = Message::avoirConversations($user['id_user']);
        $response->getBody()->write(json_encode($conversations));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function avoirMessagesParConversation(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $messages = Message::avoirMessagesParConversation(
            $args['id_advertisement'],
            $args['id_user'],
            $user['id_user']
        );

        foreach ($messages as $message) {
            if ($message['id_recipient'] == $user['id_user'] && !$message['read']) {
                $msg = new Message(
                    $message['id_message'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                );
                $msg->marquerCommeLu();
            }
        }
        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    /**
     * Summary of supprimerConversation permet de 
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function supprimerConversation(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $messages = Message::avoirMessagesParConversation(
            $args['id_advertisement'],
            $args['id_user'],
            $user['id_user']
        );
        if (empty($messages)) {
            $response->getBody()->write(json_encode(['error' => 'Conversation pas trouvée']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        foreach ($messages as $message) {
            $msg = new Message(
                $message['id_message'],
                null,
                null,
                null,
                null,
                null,
                null,
                null
            );
            $msg->delete();
        }
        $response->getBody()->write(json_encode(['message' => 'Conversation supprimée avec succès']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}