<?php
use Ramsey\Uuid\Uuid;

class AccountRepository{
    private mysqli $conn;
    public function __construct(){
        $this->conn = DatabaseManager::getInstance()->getConnection();
    }

//    public function getAccountByUsernameAndPassword($account_username, $account_password): ?Account{
//        $sql = "SELECT * FROM `Account`
//         WHERE `userName` = ? AND `password` = ?";
//        $stmt = $this->conn->prepare($sql);
//        $stmt->bind_param("ss", $account_username, $account_password);
//        $stmt->execute();
//        $result = $stmt->get_result();
//        if (!$result || $result->num_rows === 0) {
//            return null;
//        }
//        $row = $result->fetch_assoc();
//        $stmt->close();
//        return new Account($row['accountId'], $row['userName'],
//            $row['password'], $row['email'], $row['roleId']);
//    }

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

//    public function checkAccountByUsernameAndPassword($account_username, $account_password): bool{
//        $sql = "SELECT * from `Account`
//                WHERE `userName` = ? AND `password` = ?";
//        $stmt = $this->conn->prepare($sql);
//        $stmt->bind_param('ss', $account_username, $account_password);
//        $stmt->execute();
//
//        $result = $stmt->get_result();
//
//        $stmt->close();
//        return $result->num_rows === 1;
//    }

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
        // Generate UUID
        $uuid = Uuid::uuid4()->toString();

        // Default parameter
        $roleId = 1;
        $isVerified = 0;

        // Hash the password for secure storage
        $hashedPassword = password_hash($account_password, PASSWORD_DEFAULT);
        $activation_token = bin2hex(random_bytes(16));

        // Insert into the database
        $sql = "INSERT INTO `Account` 
            (`accountId`, `userName`, `password`, `email`, `activation_token`, `roleId`, `isVerified`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssssii', $uuid, $account_username, $hashedPassword, $email, $activation_token, $roleId, $isVerified);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        return new Account(
            $uuid,
            $account_username,
            $account_password,
            $email,
            $activation_token,
            $roleId,
            $isVerified
        );
    }

}

?>