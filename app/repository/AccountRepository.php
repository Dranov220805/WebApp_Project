<?php
use Ramsey\Uuid\Uuid;

class AccountRepository{
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

    public function getAccountByAccountId($accountId){
        $sql = "SELECT * FROM `Account` WHERE `accountId` = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $accountId);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return new Account(
            $row['accountId'],
            $row['userName'],
            $row['password'],
            $row['email'],
            $row['profilePicture'],
            $row['activation_token'],
            $row['refresh_token'],
            $row['expired_time'],
            $row['roleId'],
            $row['isVerified']
        );
    }

    public function getAccountByEmail($email): ?Account {
        $sql = "SELECT * FROM `Account` WHERE `email` = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        return new Account(
            $row['accountId'],
            $row['userName'],
            $row['password'],
            $row['email'],
            $row['profilePicture'],
            $row['activation_token'],
            $row['refresh_token'],
            $row['expired_time'],
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

    public function saveRefreshToken($accountId, $token, $expiry)
    {
        $sql = "UPDATE Account SET refresh_token = ?, expired_time = ? WHERE accountId = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $accountId);
        $stmt->execute();
        $stmt->close();
    }

    public function getAccountByRefreshToken($refreshToken): ?Account
    {
        $sql = "SELECT * FROM Account WHERE refresh_token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $refreshToken);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) return null;

        $row = $result->fetch_assoc();
        $stmt->close();

        return new Account(
            $row['accountId'],
            $row['userName'],
            $row['password'],
            $row['email'],
            $row['profilePicture'],
            $row['activation_token'],
            $row['refresh_token'],
            $row['expired_time'],
            $row['roleId'],
            $row['isVerified']
        );
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
        date_default_timezone_set('Asia/Bangkok');
        $accountId = Uuid::uuid4()->toString();
        $roleId = 1;
        $isVerified = 0;
        $activation_token = bin2hex(random_bytes(16));
        $hashedPassword = password_hash($account_password, PASSWORD_DEFAULT);
        $profilePicture = '';
        $refresh_token = bin2hex(random_bytes(16));
        $expired_time = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 7));

        $sql = "INSERT INTO `Account` 
        (`accountId`, `userName`, `password`, `email`, `profilePicture`, `activation_token`, `refresh_token`, `expired_time`, `roleId`, `isVerified`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssssssii', $accountId, $account_username, $hashedPassword, $email, $profilePicture, $activation_token, $refresh_token, $expired_time, $roleId, $isVerified);

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
            $profilePicture,
            $activation_token,
            $refresh_token,
            $expired_time,
            $roleId,
            $isVerified
        );
    }

    public function updateProfilePictureByAccountId($accountId, $profilePicture) {
        $sql = "UPDATE `Account` SET `profilePicture` = ? WHERE `accountId` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $profilePicture, $accountId);
        $result = $stmt->execute();
        $stmt->close();
        if ($result) {
            return [
                'status' => 'true'
            ];
        }
        else {
            return [
                'status' => 'false'
            ];
        }
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
            return new Preference(
                $row['preferenceId'],
                $row['accountId'],
                $row['layout'],
                $row['noteFont'],
                $row['noteColor'],
                $row['font'],
                $row['isDarkTheme']
            );
        } else {
            return [
                'status' => false,
                'message' => 'Account preferences not found'
            ];
        }
    }

    public function updatePreferenceByAccountId($accountId, $userName, $theme, $noteFont, $noteColor): mixed {
        $isDarkTheme = $theme === 'dark' ? 1 : 0;

        $sql = "UPDATE `Preference`
            SET `isDarkTheme` = ?,
                `noteFont` = ?,
                `noteColor` = ?
            WHERE `accountId` = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $isDarkTheme, $noteFont, $noteColor, $accountId);
        $stmt->execute();

        $accountSql = "UPDATE `Account` SET `username` = ? WHERE `accountId` = ?";
        $stmt = $this->conn->prepare($accountSql);
        $stmt->bind_param("ss", $userName, $accountId);
        $result = $stmt->execute();

        if (!$result) {
            return [
                'status' => 'false',
                'message' => 'Error updating account'
            ];
        }

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return $this->getPreferencesByAccountId($accountId);
        }

        $stmt->close();
        return [
            'status' => false,
            'message' => 'No changes made or account not found'
        ];
    }
}

?>