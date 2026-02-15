<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createMail($subject, $body) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id"
        );
        $stmt->execute([$subject, $body]);
        return (int)$stmt->fetchColumn();
    }

    public function getMail($id) {
        $stmt = $this->pdo->prepare(
            "SELECT id, subject, body FROM mail WHERE id = ?"
        );
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: false;
    }

    public function getAllMail() {
        $stmt = $this->pdo->query(
            "SELECT id, subject, body FROM mail ORDER BY id ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMail($id, $subject, $body) {
        $stmt = $this->pdo->prepare(
            "UPDATE mail SET subject = ?, body = ? WHERE id = ?"
        );
        $stmt->execute([$subject, $body, (int)$id]);
        return $stmt->rowCount() > 0;
    }

    public function deleteMail($id) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM mail WHERE id = ?"
        );
        $stmt->execute([(int)$id]);
        return $stmt->rowCount() > 0;
    }
}
