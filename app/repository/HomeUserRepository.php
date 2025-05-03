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
}

?>