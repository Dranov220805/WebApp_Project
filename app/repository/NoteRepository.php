<?php
use Ramsey\Uuid\Uuid;

class NoteRepository{
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    public function getNotePaginations($currentPageNumber, $itemsPerPage)
    {

    }

    public function getNotesByAccountIdPaginated(string $userName, int $limit, int $offset): array
    {
        $sql = "SELECT n.* FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        LEFT JOIN `Modification` m ON m.noteId = n.noteId
        WHERE a.accountId = ? 
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

    public function getPinnedNotesByAccountId($accountId): array {
        $sql = "SELECT n.* FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        LEFT JOIN `Modification` m ON m.noteId = n.noteId
        WHERE a.accountId = ? 
        AND n.isDeleted = FALSE
        AND m.isPinned = TRUE
        ORDER BY m.pinnedTime DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }
        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $note = [];
        while ($row = $result->fetch_assoc()) {
            $note[] = $row;
        }
        return $note;
    }

    public function getPinnedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
        $sql = "SELECT n.* FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        LEFT JOIN `Modification` m ON m.noteId = n.noteId
        WHERE a.accountId = ? 
        AND n.isDeleted = FALSE
        AND m.isPinned = TRUE
        ORDER BY n.createDate DESC
        LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("sii", $accountId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $notes[] = $row; // Optionally map to a Note model
        }

        return $notes;
    }

    public function createNoteByAccountIdAndTitleAndContent($accountId, $title, $content): ?Note {
        // Set timezone to UTC+7

        // Generate UUID
        $uuid = Uuid::uuid4()->toString();
        $createDate = date("Y-m-d H:i:s");
        $isDeleted = 0;
        $isProtected = 0;

        $sql = "INSERT INTO `Note` 
            (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssii", $uuid, $accountId, $title, $content, $createDate, $isDeleted, $isProtected);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        return new Note(
            $uuid,
            $accountId,
            $title,
            $content,
            $createDate,
            $isDeleted,
            $isProtected
        );
    }

    public function updateNoteByAccountIdAndNoteId($accountId, $noteId, $noteTitle, $noteContent): ?Note {
        $modifiedDate = date("Y-m-d H:i:s");
        $isDeleted = 0;
        $isProtected = 0;

        $sql = "UPDATE `Note` 
            SET `title` = ?, `content` = ?, `createDate` = ?
            WHERE `accountId` = ? AND `noteId` = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $noteTitle, $noteContent, $modifiedDate, $accountId, $noteId);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        return new Note(
            $noteId,
            $accountId,
            $noteTitle,
            $noteContent,
            $modifiedDate,
            $isDeleted, // Assuming you're not modifying isDeleted here
            $isProtected  // Assuming you're not modifying isProtected here
        );
    }

    public function searchNotesByAccountId(string $accountId, string $searchTerm): array
    {
        $sql = "SELECT * FROM `Note` 
            WHERE `accountId` = ? 
            AND `isDeleted` = FALSE 
            AND (`title` LIKE ? OR `content` LIKE ?)
            ORDER BY `createDate` DESC";

        $stmt = $this->conn->prepare($sql);
        $likeTerm = '%' . $searchTerm . '%';
        $stmt->bind_param("sss", $accountId, $likeTerm, $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }

        return $notes;
    }

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
?>