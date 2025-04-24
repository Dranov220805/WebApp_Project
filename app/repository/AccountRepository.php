<?php
use Ramsey\Uuid\Uuid;

class AccountRepository{
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    public function getAccountByEmail($email): ?Account {
        $sql = "SELECT * FROM `Account` WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return new Account(
            $row['accountId'],
            $row['userName'],
            $row['password'],
            $row['email'],
            $row['activation_token'],
            $row['roleId'],
            $row['isVerified']
        );
    }

    public function checkAccountByEmailAndPassword($email, $password): bool {
        $sql = "SELECT `password` FROM `Account` WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return password_verify($password, $row['password']);
    }

    public function activateAccountByActivationToken($token) {
        $sql = "UPDATE `Account` SET `isVerified` = 1 WHERE `activation_token` = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

    public function updateAccountPasswordByEmail($email): string{
        // Generate a random password with at least one uppercase, one lowercase, and one number
        $uppercase = chr(rand(65, 90)); // A-Z
        $lowercase = chr(rand(97, 122)); // a-z
        $number = chr(rand(48, 57)); // 0-9

        // Add more random characters to increase password length (total: 10 characters)
        $others = '';
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 7; $i++) {
            $others .= $pool[rand(0, strlen($pool) - 1)];
        }

        // Shuffle all characters to avoid predictable order
        $resetPassword = str_shuffle($uppercase . $lowercase . $number . $others);

        $hashedPassword = password_hash($resetPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE `Account` SET `password` = ? WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return $resetPassword;
        } else {
            return 'false';
        }
    }

    public function updatePasswordByEmail($email, $newPassword): bool {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE `Account` SET `password` = ? WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function getRoleByEmail($email): string {
        $sql = "SELECT `roleId` from `Account`
                WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $role_name = $row["roleId"];

        $stmt->close();
        return $role_name;
    }

    public function getRoleByUserNameAndPassword($account_username, $account_password): string {
        $sql = "SELECT `roleId` from `Account`
                WHERE `userName` = ? AND `password` = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $account_username, $account_password);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $role_name = $row["roleId"];

        $stmt->close();
        return $role_name;
    }

    public function createAccountByUsernameAndPasswordAndEmail($account_username, $account_password, $email): ?Account {
        $accountId = Uuid::uuid4()->toString();
        $roleId = 1;
        $isVerified = 0;
        $activation_token = bin2hex(random_bytes(16));
        $hashedPassword = password_hash($account_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `Account` 
        (`accountId`, `userName`, `password`, `email`, `activation_token`, `roleId`, `isVerified`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssssii', $accountId, $account_username, $hashedPassword, $email, $activation_token, $roleId, $isVerified);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        // Create default preferences after account creation
        $prefCreated = $this->createDefaultPreferencesForAccount($accountId);
        if (!$prefCreated) return null;

        return new Account(
            $accountId,
            $account_username,
            $account_password,
            $email,
            $activation_token,
            $roleId,
            $isVerified
        );
    }

    public function createDefaultPreferencesForAccount(string $accountId): bool {
        $preferenceId = Uuid::uuid4()->toString();

        // Default preferences
        $defaultLayout = 'list';
        $defaultNoteFont = '16px';
        $defaultNoteColor = '#000000';
        $defaultFont = 'Arial';
        $defaultDarkTheme = false;

        $sql = "INSERT INTO `Preference` 
            (`preferenceId`, `accountId`, `layout`, `noteFont`, `noteColor`, `font`, `isDarkTheme`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssi", $preferenceId, $accountId, $defaultLayout, $defaultNoteFont, $defaultNoteColor, $defaultFont, $defaultDarkTheme);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    public function getPreferencesByAccountId($accountId) {
        $sql = "SELECT Preference.* FROM `Preference`
            LEFT JOIN `Account` ON `Account`.`accountId` = `Preference`.`accountId`
            WHERE `Account`.`accountId` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $accountId);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();

        if ($row = $result->fetch_assoc()) {
            return [
                'status' => true,
                'preferenceId' => $row['preferenceId'],
                'accountId' => $row['accountId'],
                'layout' => $row['layout'],
                'noteFont' => $row['noteFont'],
                'noteColor' => $row['noteColor'],
                'font' => $row['font'],
                'isDarkTheme' => $row['isDarkTheme'],
                'message' => 'Account preferences found',
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Account preferences not found'
            ];
        }
    }

}

?>