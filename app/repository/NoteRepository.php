<?php

class NoteRepository {
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }
    public function getNotePaginations($currentPageNumber, $itemsPerPage) {

    }

    public function getNotesByAccountIdPaginated(string $userName, int $limit, int $offset): array {
        $sql = "SELECT n.* FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        WHERE a.userName = ? 
        AND n.isDeleted = FALSE
        ORDER BY n.createDate DESC
        LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("sii", $userName, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $notes[] = $row; // Optionally map to a Note model
        }

        return $notes;
    }

//    public function getNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
//        $sql = "SELECT * FROM `Account` a
//            LEFT JOIN `Note` n ON a.accountId = n.accountId
//            WHERE a.accountId = ?
//            LIMIT ? OFFSET ?";
//
//        $stmt = $this->conn->prepare($sql);
//        $stmt->bind_param("sii", $accountId, $limit, $offset);
//        $stmt->execute();
//        $result = $stmt->get_result();
//
//        $notes = [];
//
//        while ($row = $result->fetch_assoc()) {
//            $notes[] = $row; // You could map to a Note class if you prefer
//        }
//
//        return $notes;
//    }

}
?>