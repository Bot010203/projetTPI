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

class Message {
    public $id_message;
    public $text;
    public $timestamp;
    public $read;
    public $id_sender;
    public $id_recipient;
    public $id_advertisement;
    public $original_message_id;

    public function __construct($id_message, $text, $timestamp, $read, $id_sender, $id_recipient, $id_advertisement, $original_message_id) {
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
    public function Create() {
        $pdo = PDOSingleton::getInstance();
        $stmt = $pdo->prepare("INSERT INTO messages (text, timestamp, read, id_sender, id_recipient, id_advertisement, original_message_id) VALUES (:text, :timestamp, :read, :id_sender, :id_recipient, :id_advertisement, :original_message_id)");
        $stmt->execute([
            ':text' => $this->text,
            ':timestamp' => $this->timestamp,
            ':read' => (int)$this->read,
            ':id_sender' => $this->id_sender,
            ':id_recipient' => $this->id_recipient,
            ':id_advertisement' => $this->id_advertisement,
            ':original_message_id' => $this->original_message_id
        ]);
        $this->id_message = $pdo->lastInsertId();
    }
}