<?php
use Ramsey\Uuid\Uuid;

class HomeUserRepository{
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    public function getLabelByAccountId($accountId) {
        $sql = "SELECT DISTINCT Label.* 
            FROM `Label`
            WHERE Label.accountId = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $accountId);
        $stmt->execute();

        $result = $stmt->get_result();
        $labels = [];

        while ($row = $result->fetch_assoc()) {
            $labels[] = $row['labelName'];
        }

        $stmt->close();

        return $labels;
    }

    public function getLabelNotesByAccountIdAndLabelName($accountId, $labelName) {
        return [
            'status' => true,
            'message' => 'ok'
        ];
    }

    public function getSharedEmailByNoteIdAndEmail($noteId, $email): array {
        $sql = "SELECT NoteSharing.*
            FROM `NoteSharing`
            WHERE NoteSharing.sharedEmail = ? AND NoteSharing.noteId = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $email, $noteId);
        $stmt->execute();

        $result = $stmt->get_result();
        $sharedList = [];

        while ($row = $result->fetch_assoc()) {
            $sharedList[] = $row;
        }

        $stmt->close();

        return $sharedList;
    }

    public function addSharedEmailByNoteIdAndEmailAndNewEmail($noteId, $email, $newEmail) {
        $noteSharingId = Uuid::uuid4()->toString();
        $timeShared = date("Y-m-d H:i:s");

        $sql = "INSERT INTO `NoteSharing` (`noteSharingId`, `noteId`, `sharedEmail`, `receivedEmail`, `timeShared`, `canEdit`)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $canEdit = false;

        $stmt->bind_param('sssssi', $noteSharingId, $noteId, $email, $newEmail, $timeShared, $canEdit);
        $stmt->execute();
        $stmt->close();

        return new NoteSharing(
            $noteSharingId,
            $noteId,
            $email,
            $newEmail,
            $timeShared,
            $canEdit
        );
    }

    public function isNoteAlreadySharedTo($noteId, $email): bool {
        $sql = "SELECT * FROM NoteSharing WHERE noteId = ? AND sharedEmail = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $noteId, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $alreadyShared = $result && $result->num_rows > 0;
        $stmt->close();
        return $alreadyShared;
    }


}

?>