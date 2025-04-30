<?php

use Ramsey\Uuid\Uuid;

class NoteRepository
{
    private mysqli $conn;
    public function __construct()
    {
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    public function getNotesByAccountId(string $accountId)
    {
        $sql = "SELECT n.* 
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            WHERE a.accountId = ? 
              AND n.isDeleted = FALSE
              AND (m.isPinned IS NULL OR m.isPinned = FALSE)
            ORDER BY n.createDate DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        //        $stmt->bind_param("sii", $userName, $limit, $offset);
        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $notes[] = $row; // Optionally map to a Note model
        }

        return $notes;
    }

    public function getNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array
    {
        $sql = "SELECT n.* 
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            WHERE a.accountId = ? 
            AND n.isDeleted = FALSE
            AND (m.isPinned IS NULL OR m.isPinned = FALSE)
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

    public function getPinnedNotesByAccountId($accountId): array
    {
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

    public function getPinnedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array
    {
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

    public function getTrashedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
        $sql = "SELECT n.* 
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            WHERE a.accountId = ? 
            AND n.isDeleted = TRUE
            AND (m.isPinned IS NULL OR m.isPinned = FALSE)
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
            $notes[] = $row;
        }

        return $notes;
    }

    public function createNoteByAccountIdAndTitleAndContent($accountId, $title, $content): ?Note
    {
        // Set timezone to UTC+7
        date_default_timezone_set('Asia/Bangkok');

        // Generate UUID for the new note
        $uuid = Uuid::uuid4()->toString();
        $createDate = date("Y-m-d H:i:s");
        $isDeleted = 0;
        $isProtected = 0;

        // Insert the new note into the `Note` table
        $sql = "INSERT INTO `Note` 
        (`noteId`, `accountId`, `title`, `content`, `createDate`, `isDeleted`, `isProtected`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssii", $uuid, $accountId, $title, $content, $createDate, $isDeleted, $isProtected);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        // Now, insert the corresponding modification record for this new note
        $this->createModificationRecord($accountId, $uuid);

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

    private function createModificationRecord($accountId, $noteId)
    {
        // Set timezone to UTC+7
        date_default_timezone_set('Asia/Bangkok');
        $modifyId = Uuid::uuid4()->toString();
        $pinnedTime = null;  // New notes will not be pinned initially
        $isPinned = false;
        $modifiedDate = date("Y-m-d H:i:s");

        // Insert a new record into the `Modification` table for the newly created note
        $sql = "INSERT INTO `Modification` 
            (`modifyId`, `noteId`, `isPinned`, `pinnedTime`) 
            VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssis", $modifyId, $noteId, $isPinned, $pinnedTime);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) {
            throw new Exception("Failed to create modification record for the new note.");
        }
    }

    public function updateNoteByAccountIdAndNoteId($accountId, $noteId, $noteTitle, $noteContent): ?Note
    {
        date_default_timezone_set('Asia/Bangkok');
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

    public function deleteNoteByAccountIdAndNoteId($accountId, $noteId): bool {
        date_default_timezone_set('Asia/Bangkok');
        $deletedDate = date("Y-m-d H:i:s");

        $sql = "UPDATE `Note` 
            SET `isDeleted` = TRUE, `createDate` = ? 
            WHERE `accountId` = ? AND `noteId` = ? AND `isDeleted` = FALSE";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("sss", $deletedDate, $accountId, $noteId);
        $result = $stmt->execute();
        $stmt->close();

        if (!$result || $this->conn->affected_rows === 0) {
            return false; // No row was updated, either it was already deleted or not found
        }

        return true;
    }

    public function pinNoteByNoteId($noteId): bool
    {
        // Ensure $noteId is a string (for debugging)
        if (is_array($noteId)) {
            throw new Exception("Note ID should be a string, but an array was passed.");
        }

        // Set the timezone to Asia/Bangkok (GMT+7)
        date_default_timezone_set('Asia/Bangkok');
        $pinnedDate = date("Y-m-d H:i:s");

        // Update Modification table with the noteId alone, no need for accountId
        $sql = "UPDATE `Modification` 
            SET `isPinned` = TRUE, `pinnedTime` = ? 
            WHERE `noteId` = ?";  // No accountId in the WHERE clause

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        // Bind parameters: pinnedDate, noteId
        $stmt->bind_param("ss", $pinnedDate, $noteId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function unpinNoteByNoteId($noteId): bool
    {
        // Ensure $noteId is a string (for debugging)
        if (is_array($noteId)) {
            throw new Exception("Note ID should be a string, but an array was passed.");
        }

        // Set the timezone to Asia/Bangkok (GMT+7)
        date_default_timezone_set('Asia/Bangkok');
        $pinnedDate = date("Y-m-d H:i:s");

        // Update Modification table with the noteId alone, no need for accountId
        $sql = "UPDATE `Modification` 
            SET `isPinned` = FALSE, `pinnedTime` = ? 
            WHERE `noteId` = ?";  // No accountId in the WHERE clause

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        // Bind parameters: pinnedDate, noteId
        $stmt->bind_param("ss", $pinnedDate, $noteId);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

}
