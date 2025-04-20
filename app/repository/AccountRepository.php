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
            $row['roleId']
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

        // Hash the password for secure storage
        $hashedPassword = password_hash($account_password, PASSWORD_DEFAULT);

        // Insert into the database
        $sql = "INSERT INTO `Account` 
            (`accountId`, `userName`, `password`, `email`, `roleId`) 
            VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssssi', $uuid, $account_username, $hashedPassword, $email, $roleId);

        $result = $stmt->execute();
        $stmt->close();

        if (!$result) return null;

        return new Account(
            $uuid,
            $account_username,
            $account_password,
            $email,
            $roleId
        );
    }

}

?>