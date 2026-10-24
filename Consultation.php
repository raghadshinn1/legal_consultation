<?php
declare(strict_types=1);

class Consultation {
    private PDO $conn;
    private string $table = 'consultations';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function create(int $clientId, int $lawyerId, string $subject, string $description): bool {
        $query = "INSERT INTO " . $this->table . " (client_id, lawyer_id, subject, description) VALUES (:client_id, :lawyer_id, :subject, :description)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->bindParam(':lawyer_id', $lawyerId, PDO::PARAM_INT);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':description', $description);

        return $stmt->execute();
    }

    public function getByClientId(int $clientId): array {
        $query = "SELECT * FROM " . $this->table . " WHERE client_id = :client_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array {
        $query = "SELECT consultations.*, users.name AS client_name 
                  FROM " . $this->table . " 
                  JOIN users ON consultations.client_id = users.id 
                  ORDER BY consultations.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByLawyerId(int $lawyerId): array {
        $query = "SELECT consultations.*, users.name AS client_name 
                  FROM " . $this->table . " 
                  JOIN users ON consultations.client_id = users.id 
                  WHERE consultations.lawyer_id = :lawyer_id 
                  ORDER BY consultations.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lawyer_id', $lawyerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}