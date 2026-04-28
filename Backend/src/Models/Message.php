<?php
/**
 * Nom du projet : ProjetTPI
 * Auteur : Paul Chiacchiari
 * Date : 22.04.2026
 * Nom fichier : Message.php
 * But : Classe Message avec CRUD pour gérer les messages du site web
 */
namespace App\Models;

use App\Models\PDOSingleton;
use PDO;
use App\Models\Annonce;
use App\Models\User;

class Message
{
    public $id_message;
    public $text;
    public $timestamp;
    public $read;
    public $id_sender;
    public $id_recipient;
    public $id_advertisement;
    public $original_message_id;

    public function __construct($id_message, $text, $timestamp, $read, $id_sender, $id_recipient, $id_advertisement, $original_message_id)
    {
        $this->id_message = $id_message;
        $this->text = $text;
        $this->timestamp = $timestamp;
        $this->read = $read;
        $this->id_sender = $id_sender;
        $this->id_recipient = $id_recipient;
        $this->id_advertisement = $id_advertisement;
        $this->original_message_id = $original_message_id;
    }

    /**
     * Summary of Create permet de créer un message dans la base de données
     * @return void
     */
    public function create()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO messages (text, `timestamp`, `read`, id_sender, id_recipient, id_advertisement, original_message_id) VALUES (:text, :timestamp, :read, :id_sender, :id_recipient, :id_advertisement, :original_message_id)");
        $stmt->execute([
            ':text' => $this->text,
            ':timestamp' => $this->timestamp,
            ':read' => (int) $this->read,
            ':id_sender' => $this->id_sender,
            ':id_recipient' => $this->id_recipient,
            ':id_advertisement' => $this->id_advertisement,
            ':original_message_id' => $this->original_message_id
        ]);
        $this->id_message = $pdo->lastInsertId();
    }

    public function delete()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id_message = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
    }
    /**
     * Summary of read
     * @return void
     */
    public function read()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id_message = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
        $message = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($message) {
            $this->text = $message['text'];
            $this->timestamp = $message['timestamp'];
            $this->read = (bool) $message['read'];
            $this->id_sender = $message['id_sender'];
            $this->id_recipient = $message['id_recipient'];
            $this->id_advertisement = $message['id_advertisement'];
            $this->original_message_id = $message['original_message_id'];
        }
    }
    /**
     * Summary of readOriginal 
     * @return array
     */
    public function readOriginal()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE original_message_id = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Summary of marquerCommeLu permet de savoir si un message a été lu
     * @return void
     */
    public function marquerCommeLu()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE messages SET `read` = 1 WHERE id_message = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
    }
    public static function readByUserId($id_user)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id_sender = :id_user OR id_recipient = :id_user ORDER BY timestamp DESC");
        $stmt->execute([':id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function avoirConversations($id_user)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("
        SELECT messages.*, advertisements.title, users.login
        FROM messages
        JOIN advertisements ON advertisements.id_advertisement = messages.id_advertisement
        JOIN users ON (messages.id_sender = :id_user1 AND users.id_user = messages.id_recipient)
                   OR (messages.id_recipient = :id_user2 AND users.id_user = messages.id_sender)
        WHERE messages.id_message IN (
            SELECT MAX(id_message)
            FROM messages
            WHERE id_sender = :id_user3 OR id_recipient = :id_user4
            GROUP BY id_advertisement
        )
        ORDER BY messages.timestamp DESC
    ");
        $stmt->execute([
            ':id_user1' => $id_user,
            ':id_user2' => $id_user,
            ':id_user3' => $id_user,
            ':id_user4' => $id_user
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function avoirMessagesParConversation($id_advertisement, $id_user1, $id_user2)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT messages.*, users.login
            FROM messages
            JOIN users ON users.id_user=messages.id_sender
            WHERE messages.id_advertisement=:id_advertisement
            AND ((messages.id_sender=:id_user1 AND messages.id_recipient= :id_user2)
            OR (messages.id_sender = :id_user2b AND messages.id_recipient = :id_user1b))
            ORDER BY messages.timestamp DESC
        ");
        $stmt->execute([
            ':id_advertisement' => $id_advertisement,
            ':id_user1' => $id_user1,
            ':id_user2' => $id_user2,
            ':id_user1b' => $id_user1,
            ':id_user2b' => $id_user2
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}