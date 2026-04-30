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
        $stmt = $pdo->prepare("INSERT INTO Message (text, `timestamp`, `read`, id_sender, id_recipient, id_advertisement, original_message_id) VALUES (:text, :timestamp, :read, :id_sender, :id_recipient, :id_advertisement, :original_message_id)");
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
        $stmt = $pdo->prepare("DELETE FROM Message WHERE id_message = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
    }
    /**
     * Summary of read
     * @return void
     */
    public function read()
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Message WHERE id_message = :id_message");
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
        $stmt = $pdo->prepare("SELECT * FROM Message WHERE original_message_id = :id_message");
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
        $stmt = $pdo->prepare("UPDATE Message SET `read` = 1 WHERE id_message = :id_message");
        $stmt->execute([':id_message' => $this->id_message]);
    }
    public static function readByUserId($id_user)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM Message WHERE id_sender = :id_user1 OR id_recipient = :id_user2 ORDER BY timestamp DESC");
        $stmt->execute([
            ':id_user1' => $id_user,
            ':id_user2' => $id_user
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function avoirConversations($id_user)
    {
        $pdo = PDOSingleton::getInstance()->getConnection();
        $stmt = $pdo->prepare("
        SELECT Message.*, Advertisement.title, User.login
        FROM Message
        JOIN Advertisement ON Advertisement.id_advertisement = Message.id_advertisement
        JOIN User ON (Message.id_sender = :id_user1 AND User.id_user = Message.id_recipient)
                   OR (Message.id_recipient = :id_user2 AND User.id_user = Message.id_sender)
        WHERE Message.id_message IN (
            SELECT MAX(id_message)
            FROM Message
            WHERE id_sender = :id_user3 OR id_recipient = :id_user4
            GROUP BY id_advertisement
        )
        ORDER BY Message.timestamp DESC
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
            SELECT Message.*, User.login
            FROM Message
            JOIN User ON User.id_user=Message.id_sender
            WHERE Message.id_advertisement=:id_advertisement
            AND ((Message.id_sender=:id_user1 AND Message.id_recipient= :id_user2)
            OR (Message.id_sender = :id_user2b AND Message.id_recipient = :id_user1b))
            ORDER BY Message.timestamp DESC
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