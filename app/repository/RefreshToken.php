<?php

namespace Repository;

//use PDO;

use DatabaseManager;
use mysqli;

class RefreshToken {
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    // Insert a new refresh token into the database
    public static function create($userId, $token, $expiresAt) {
        $db = DatabaseManager::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("INSERT INTO refresh_tokens (accountId, token, expiresAt) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $token, $expiresAt]);
        } catch (\PDOException $e) {
            // Log the exception message or handle the error accordingly
            error_log("Error creating refresh token: " . $e->getMessage());
            throw new \Exception("Error creating refresh token.");
        }
    }

    // Find a valid refresh token in the database
    public static function findValid($token) {
        $db = DatabaseManager::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("SELECT * FROM refresh_tokens WHERE token = ? AND revoked = 0 AND expiresAt > NOW()");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the exception or handle the error
            error_log("Error fetching refresh token: " . $e->getMessage());
            throw new \Exception("Error fetching refresh token.");
        }
    }

    // Update the usage of a refresh token
    public static function updateUsage($id, $expiresAt) {
        $db = DatabaseManager::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("UPDATE refresh_tokens SET lastUsedAt = NOW(), expiresAt = ? WHERE id = ?");
            $stmt->execute([$expiresAt, $id]);
        } catch (\PDOException $e) {
            // Log the exception or handle the error
            error_log("Error updating refresh token usage: " . $e->getMessage());
            throw new \Exception("Error updating refresh token usage.");
        }
    }

    // Revoke a refresh token (set revoked flag to 1)
    public static function revoke($token) {
        $db = DatabaseManager::getInstance()->getConnection();

        try {
            $stmt = $db->prepare("UPDATE refresh_tokens SET revoked = 1 WHERE token = ?");
            $stmt->execute([$token]);
        } catch (\PDOException $e) {
            // Log the exception or handle the error
            error_log("Error revoking refresh token: " . $e->getMessage());
            throw new \Exception("Error revoking refresh token.");
        }
    }
}

?>
