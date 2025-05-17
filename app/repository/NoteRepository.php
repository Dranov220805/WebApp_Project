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
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName
                FROM `Account` a
                LEFT JOIN `Note` n ON a.accountId = n.accountId
                LEFT JOIN `Modification` m ON m.noteId = n.noteId
                LEFT JOIN `Image` i ON i.noteId = n.noteId
                LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
                LEFT JOIN `Label` l ON l.labelId = nl.labelId
                WHERE a.accountId = ? 
                  AND n.isDeleted = FALSE
                  AND (m.isPinned IS NULL OR m.isPinned = FALSE)
                ORDER BY n.createDate DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }

        return $notes;
    }

    public function getNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array
    {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            LEFT JOIN `Image` i on i.noteId = n.noteId
            LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
            LEFT JOIN `Label` l ON l.labelId = nl.labelId
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
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getPinnedNotesByAccountId($accountId): array
    {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        LEFT JOIN `Modification` m ON m.noteId = n.noteId
        LEFT JOIN `Image` i on i.noteId = n.noteId
        LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
        LEFT JOIN `Label` l ON l.labelId = nl.labelId
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

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getPinnedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array
    {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName FROM `Account` a
        LEFT JOIN `Note` n ON a.accountId = n.accountId
        LEFT JOIN `Modification` m ON m.noteId = n.noteId
        LEFT JOIN `Image` i on i.noteId = n.noteId
        LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
        LEFT JOIN `Label` l ON l.labelId = nl.labelId
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
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getTrashedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Image` i on i.noteId = n.noteId
            LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
            LEFT JOIN `Label` l ON l.labelId = nl.labelId
            WHERE a.accountId = ? 
            AND n.isDeleted = TRUE
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
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getTrashedNotesByAccountId($accountId): array {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            LEFT JOIN `Image` i on i.noteId = n.noteId
            LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
            LEFT JOIN `Label` l ON l.labelId = nl.labelId
            WHERE a.accountId = ? 
            AND n.isDeleted = TRUE
            ORDER BY n.createDate DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getLabelNoteByLabelName(string $labelName, string $accountId) {
        $sql = "SELECT Note.*, Image.imageLink, Label.labelId, Label.labelName
            FROM Label
            INNER JOIN NoteLabel ON Label.labelId = NoteLabel.labelId
            INNER JOIN Note ON Note.noteId = NoteLabel.noteId
            LEFT JOIN Image ON Note.noteId = Image.noteId
            WHERE Label.labelName = ? 
              AND Label.accountId = ? 
              AND Note.isDeleted = FALSE";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $labelName, $accountId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getSharedNoteByAccountId($accountId): array {
        $sql = "SELECT n.*, i.imageLink, l.labelId, l.labelName
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            LEFT JOIN `Image` i on i.noteId = n.noteId
            LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
            LEFT JOIN `Label` l ON l.labelId = nl.labelId
            WHERE a.accountId = ? 
            AND n.isDeleted = FALSE
            ORDER BY n.createDate DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Failed to prepare SQL statement: " . $this->conn->error);
        }

        $stmt->bind_param("s", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $noteId = $row['noteId'];

            if (!isset($notes[$noteId])) {
                $notes[$noteId] = $row;
                $notes[$noteId]['labels'] = [];
            }

            if ($row['labelId']) {
                $notes[$noteId]['labels'][] = [
                    'labelId' => $row['labelId'],
                    'labelName' => $row['labelName']
                ];
            }
        }

        return array_values($notes);
    }

    public function getNotesSharedByEmail($email): array {
        $sql = "
            SELECT 
                ns.*, 
                n.*,
                i.imageLink, 
                l.labelId, 
                l.labelName
            FROM NoteSharing ns
            INNER JOIN Note n ON ns.noteId = n.noteId
            LEFT JOIN Image i ON i.noteId = n.noteId
            LEFT JOIN NoteLabel nl ON nl.noteId = n.noteId
            LEFT JOIN Label l ON l.labelId = nl.labelId
            WHERE ns.receivedEmail = ? AND n.isDeleted = FALSE
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $notes = [];

        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }

        $stmt->close();
        return $notes;
    }

    public function shareNoteBySharedAccountIdAndReceivedAccountId($noteId, $sharedAccountId, $receivedAccountId) {
        date_default_timezone_set('Asia/Bangkok');

        $noteSharingId = Uuid::uuid4()->toString();
        $timeShared = date("Y-m-d H:i:s");
        $canEdit = 0;

        $sql = "INSERT INTO NoteSharing (noteSharingId, noteId, sharedEmail, receivedEmail, timeShared, canEdit) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssi", $noteSharingId, $noteId, $sharedAccountId, $receivedAccountId, $timeShared, $canEdit);
        $result = $stmt->execute();
        $stmt->close();

        if (!$result) {
            return null;
        }

        return new NoteSharing(
            $noteSharingId,
            $noteId,
            $sharedAccountId,
            $receivedAccountId,
            $timeShared,
            $canEdit
        );

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
        $sql = "
            SELECT 
                n.*,
                GROUP_CONCAT(DISTINCT i.imageLink) AS imageLinks,
                GROUP_CONCAT(DISTINCT l.labelName) AS labels
            FROM `Account` a
            LEFT JOIN `Note` n ON a.accountId = n.accountId
            LEFT JOIN `Modification` m ON m.noteId = n.noteId
            LEFT JOIN `Image` i ON i.noteId = n.noteId
            LEFT JOIN `NoteLabel` nl ON nl.noteId = n.noteId
            LEFT JOIN `Label` l ON l.labelId = nl.labelId
            WHERE a.accountId = ? 
            AND n.isDeleted = FALSE 
            AND (n.title LIKE ? OR n.content LIKE ?)
            GROUP BY n.noteId
            ORDER BY n.createDate DESC
        ";

        $stmt = $this->conn->prepare($sql);
        $likeTerm = '%' . $searchTerm . '%';
        $stmt->bind_param("sss", $accountId, $likeTerm, $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();

        $notes = [];
        while ($row = $result->fetch_assoc()) {
            // Optional: explode comma-separated strings into arrays
            if (isset($row['imageLinks'])) {
                $row['imageLinks'] = explode(',', $row['imageLinks']);
            }
            if (isset($row['labels'])) {
                $row['labels'] = explode(',', $row['labels']);
            }
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

    public function restoreNoteByAccountIdAndNoteId($accountId, $noteId): bool {
        date_default_timezone_set('Asia/Bangkok');
        $deletedDate = date("Y-m-d H:i:s");

        $sql = "UPDATE `Note` 
            SET `isDeleted` = FALSE, `createDate` = ? 
            WHERE `accountId` = ? AND `noteId` = ? AND `isDeleted` = TRUE";

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

    public function hardDeleteNoteByAccountIdAndNoteId($accountId, $noteId): bool
    {
        // Delete the modification record first to avoid foreign key constraint errors
        $modSql = "DELETE FROM `Modification` WHERE `noteId` = ?";
        $modStmt = $this->conn->prepare($modSql);
        $modStmt->bind_param("s", $noteId);
        $modResult = $modStmt->execute();
        $modStmt->close();

        if (!$modResult) return false;

        // Then delete the note
        $noteSql = "DELETE FROM `Note` WHERE `noteId` = ? AND `accountId` = ?";
        $noteStmt = $this->conn->prepare($noteSql);
        $noteStmt->bind_param("ss", $noteId, $accountId);
        $noteResult = $noteStmt->execute();
        $noteStmt->close();

        return $noteResult;
    }

    public function updateLabelByLabelName(string $oldLabelName, string $newLabelName) {
        $sql = "UPDATE `Label` 
            SET `labelName` = ? 
            WHERE `labelName` = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('ss', $newLabelName, $oldLabelName);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function createLabelByLabelName(string $labelName, string $accountId) {
        $sql = "INSERT INTO `Label` (`labelId`, `accountId`, `labelName`) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $labelId = Uuid::uuid4()->toString();
        $stmt->bind_param('sss', $labelId, $accountId, $labelName);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function deleteNoteLabelByLabelName(string $labelName, string $accountId) {
        $sql = "DELETE NoteLabel FROM NoteLabel
                INNER JOIN `Label` l ON l.labelId = NoteLabel.labelId
                WHERE l.labelName = ? AND l.accountId = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('ss', $labelName, $accountId);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function deleteLabelByLabelNameAndAccountId(string $labelName, string $accountId) {
        $sql = "DELETE FROM `Label` WHERE `labelName` = ? AND `accountId` = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('ss', $labelName, $accountId);
        $success = $stmt->execute();
        $stmt->close();

        $this->deleteNoteLabelByLabelName($labelName, $accountId);

        return $success;
    }

    public function createNoteLabelByLabelNameAndNoteIdAndAccountId(string $labelName, string $noteId, string $accountId): bool {
        // Get labelId from Label table
        $sqlLabel = "SELECT labelId FROM Label WHERE labelName = ? AND accountId = ? AND isDeleted = FALSE";

        $stmtLabel = $this->conn->prepare($sqlLabel);
        if (!$stmtLabel) return false;

        $stmtLabel->bind_param('ss', $labelName, $accountId);
        $stmtLabel->execute();

        $resultLabel = $stmtLabel->get_result();
        $labelRow = $resultLabel->fetch_assoc();
        $stmtLabel->close();

        if (!$labelRow) return false;
        $labelId = $labelRow['labelId'];

        // Insert into NoteLabel
        $noteLabelId = Uuid::uuid4()->toString();

        $sqlInsert = "INSERT INTO NoteLabel (noteLabelId, labelId, noteId) VALUES (?, ?, ?)";

        $stmtInsert = $this->conn->prepare($sqlInsert);
        if (!$stmtInsert) return false;

        $stmtInsert->bind_param('sss', $noteLabelId, $labelId, $noteId);
        $success = $stmtInsert->execute();
        $stmtInsert->close();

        return $success;
    }

    public function deleteNoteLabelByLabelNameAndNoteIdAndAccountId(string $labelName, string $noteId, string $accountId) {
        // Get labelId from Label table
        $sqlLabel = "SELECT labelId FROM Label WHERE labelName = ? AND accountId = ? AND isDeleted = FALSE";

        $stmtLabel = $this->conn->prepare($sqlLabel);
        if (!$stmtLabel) return false;

        $stmtLabel->bind_param('ss', $labelName, $accountId);
        $stmtLabel->execute();

        $resultLabel = $stmtLabel->get_result();
        $labelRow = $resultLabel->fetch_assoc();
        $stmtLabel->close();

        if (!$labelRow) return false;
        $labelId = $labelRow['labelId'];

        $sqlDelete = "DELETE FROM `NoteLabel` WHERE `noteId` = ? AND `labelId` = ?";

        $stmtDelete = $this->conn->prepare($sqlDelete);
        if (!$stmtDelete) return false;

        $stmtDelete->bind_param('ss', $noteId, $labelId);
        $success = $stmtDelete->execute();
        $stmtDelete->close();

        return $success;
    }

    public function createImageForNoteByImageUrlAndNoteId(string $imageUrl, string $noteId) {
        // Hard delete existing image(s) for the note
        $deleteSql = "DELETE FROM Image WHERE noteId = ?";
        $deleteStmt = $this->conn->prepare($deleteSql);
        if (!$deleteStmt) {
            return [
                'status' => false,
                'message' => 'Failed to prepare delete statement: ' . $this->conn->error
            ];
        }
        $deleteStmt->bind_param("s", $noteId);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Insert new image
        $insertSql = "INSERT INTO Image (imageId, noteId, title, imageLink, isDeleted) VALUES (?, ?, ?, ?, FALSE)";
        $insertStmt = $this->conn->prepare($insertSql);
        if (!$insertStmt) {
            return [
                'status' => false,
                'message' => 'Failed to prepare insert statement: ' . $this->conn->error
            ];
        }

        $imageId = Uuid::uuid4()->toString();
        $title = basename(parse_url($imageUrl, PHP_URL_PATH));

        $insertStmt->bind_param("ssss", $imageId, $noteId, $title, $imageUrl);

        if ($insertStmt->execute()) {
            $insertStmt->close();
            return [
                'status' => true,
                'imageId' => $imageId,
                'title' => $title,
                'imageLink' => $imageUrl
            ];
        } else {
            $insertStmt->close();
            return [
                'status' => false,
                'message' => 'Failed to insert image: ' . $insertStmt->error
            ];
        }
    }

    public function deleteImageForNoteByImageUrlAndNoteId(string $imageUrl, string $noteId): array {
        $deleteSql = "DELETE FROM Image WHERE noteId = ? AND imageLink = ?";
        $deleteStmt = $this->conn->prepare($deleteSql);

        if (!$deleteStmt) {
            return [
                'status' => false,
                'message' => 'Failed to prepare delete statement: ' . $this->conn->error
            ];
        }

        $deleteStmt->bind_param("ss", $noteId, $imageUrl);

        if ($deleteStmt->execute()) {
            $affectedRows = $deleteStmt->affected_rows;
            $deleteStmt->close();

            if ($affectedRows > 0) {
                return [
                    'status' => true,
                    'message' => 'Image deleted successfully.'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'No matching image found to delete.'
                ];
            }
        } else {
            $deleteStmt->close();
            return [
                'status' => false,
                'message' => 'Failed to execute delete statement: ' . $deleteStmt->error
            ];
        }
    }

    public function protectedNoteByNoteIdAndAccountId(string $noteId, string $accountId, string $password): bool {
        // Check if the note exists and belongs to the account
        $checkSql = "SELECT * FROM `Note` WHERE noteId = ? AND accountId = ? AND isDeleted = FALSE";
        $stmt = $this->conn->prepare($checkSql);
        $stmt->bind_param("ss", $noteId, $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false; // Note not found or doesn't belong to the user
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if there's already a protection record
        $checkProtectSql = "SELECT * FROM `NoteProtect` WHERE noteId = ? AND isDeleted = FALSE";
        $stmt = $this->conn->prepare($checkProtectSql);
        $stmt->bind_param("s", $noteId);
        $stmt->execute();
        $protectResult = $stmt->get_result();

        if ($protectResult->num_rows > 0) {
            // Update existing protection
            $updateSql = "UPDATE `NoteProtect` SET password = ?, isEnabled = TRUE, isDeleted = FALSE WHERE noteId = ?";
            $stmt = $this->conn->prepare($updateSql);
            $stmt->bind_param("ss", $hashedPassword, $noteId);
        } else {
            // Insert new protection record
            $noteProtectId = Uuid::uuid4()->toString();
            $insertSql = "INSERT INTO `NoteProtect` (noteProtectId, noteId, password, isEnabled, isDeleted) VALUES (?, ?, ?, TRUE, FALSE)";
            $stmt = $this->conn->prepare($insertSql);
            $stmt->bind_param("sss", $noteProtectId, $noteId, $hashedPassword);
        }

        if (!$stmt->execute()) {
            return false;
        }

        // Update the Note to mark it as protected
        $updateNoteSql = "UPDATE `Note` SET isProtected = TRUE WHERE noteId = ?";
        $stmt = $this->conn->prepare($updateNoteSql);
        $stmt->bind_param("s", $noteId);

        return $stmt->execute();
    }

    public function checkPasswordNoteByNoteIdAndAccountId(string $noteId, string $accountId, string $password): bool
    {
        // Make sure the note exists and belongs to the account
        $noteSql = "SELECT * FROM `Note` WHERE noteId = ? AND accountId = ? AND isDeleted = FALSE AND isProtected = TRUE";
        $stmt = $this->conn->prepare($noteSql);
        $stmt->bind_param("ss", $noteId, $accountId);
        $stmt->execute();
        $noteResult = $stmt->get_result();

        if ($noteResult->num_rows === 0) {
            return false;
        }

        // Fetch the protected password
        $protectSql = "SELECT password FROM `NoteProtect` WHERE noteId = ? AND isDeleted = FALSE AND isEnabled = TRUE";
        $stmt = $this->conn->prepare($protectSql);
        $stmt->bind_param("s", $noteId);
        $stmt->execute();
        $protectResult = $stmt->get_result();

        if ($protectResult->num_rows === 0) {
            return false;
        }

        $row = $protectResult->fetch_assoc();
        return password_verify($password, $row['password']);
    }

    public function deletePasswordNoteByNoteIdAndAccountId(string $noteId, string $accountId, string $inputPassword): bool
    {
        // Confirm the note exists and belongs to the user
        $checkNoteSql = "SELECT * FROM `Note` WHERE noteId = ? AND accountId = ? AND isDeleted = FALSE AND isProtected = TRUE";
        $stmt = $this->conn->prepare($checkNoteSql);
        $stmt->bind_param("ss", $noteId, $accountId);
        $stmt->execute();
        $noteResult = $stmt->get_result();

        if ($noteResult->num_rows === 0) {
            return false;
        }

        // Retrieve the stored password hash
        $protectSql = "SELECT password FROM `NoteProtect` WHERE noteId = ? AND isDeleted = FALSE AND isEnabled = TRUE";
        $stmt = $this->conn->prepare($protectSql);
        $stmt->bind_param("s", $noteId);
        $stmt->execute();
        $protectResult = $stmt->get_result();

        if ($protectResult->num_rows === 0) {
            return false;
        }

        $row = $protectResult->fetch_assoc();
        $hashedPassword = $row['password'];

        // Verify the password
        if (!password_verify($inputPassword, $hashedPassword)) {
            return false; // Password does not match
        }

        // Disable and soft-delete the NoteProtect entry
        $disableSql = "UPDATE `NoteProtect` SET isDeleted = TRUE, isEnabled = FALSE WHERE noteId = ?";
        $stmt = $this->conn->prepare($disableSql);
        $stmt->bind_param("s", $noteId);
        if (!$stmt->execute()) {
            return false;
        }

        //  Mark the note as not protected
        $updateNoteSql = "UPDATE `Note` SET isProtected = FALSE WHERE noteId = ?";
        $stmt = $this->conn->prepare($updateNoteSql);
        $stmt->bind_param("s", $noteId);

        return $stmt->execute();
    }

    public function changeNotePasswordByNoteIdAndAccountId(string $noteId, string $accountId, string $currentPassword, string $newPassword): bool {
        // Validate note ownership and protection status
        $checkNoteSql = "SELECT * FROM `Note` WHERE noteId = ? AND accountId = ? AND isDeleted = FALSE AND isProtected = TRUE";
        $stmt = $this->conn->prepare($checkNoteSql);
        $stmt->bind_param("ss", $noteId, $accountId);
        $stmt->execute();
        $noteResult = $stmt->get_result();

        if ($noteResult->num_rows === 0) {
            return false; // Note doesn't exist or not owned by user
        }

        // Retrieve the current password hash from NoteProtect
        $protectSql = "SELECT noteProtectId, password FROM `NoteProtect` WHERE noteId = ? AND isDeleted = FALSE AND isEnabled = TRUE";
        $stmt = $this->conn->prepare($protectSql);
        $stmt->bind_param("s", $noteId);
        $stmt->execute();
        $protectResult = $stmt->get_result();

        if ($protectResult->num_rows === 0) {
            return false; // No protection record found
        }

        $row = $protectResult->fetch_assoc();
        $storedHash = $row['password'];
        $noteProtectId = $row['noteProtectId'];

        // Verify the current password
        if (!password_verify($currentPassword, $storedHash)) {
            return false; // Password does not match
        }

        // Hash the new password
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in NoteProtect
        $updateSql = "UPDATE `NoteProtect` SET password = ? WHERE noteProtectId = ?";
        $stmt = $this->conn->prepare($updateSql);
        $stmt->bind_param("ss", $newHashedPassword, $noteProtectId);

        return $stmt->execute();
    }

}
