<?php
/**
 * UserManager Class
 * Handles all user-related database operations with prepared statements
 */
class UserManager {
    private $pdo;

    /**
     * Constructor
     * @param PDO $pdo Database connection object
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get user by username or email
     * @param string $identifier Username or email
     * @return array|null User data or null if not found
     */
    public function getUserByIdentifier($identifier) {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, username, email, password_hash, is_active FROM Users WHERE username = :identifier OR email = :identifier LIMIT 1'
        );
        $stmt->execute(['identifier' => $identifier]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($userId) {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, username, email, password_hash, is_active, is_admin FROM Users WHERE user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if username or email already exists
     * @param string $username Username
     * @param string $email Email
     * @return array|null Existing user data or null
     */
    public function checkUserExists($username, $email) {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM Users WHERE username = :username OR email = :email LIMIT 1'
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user account
     * @param string $username Username
     * @param string $email Email address
     * @param string $password Plain text password (will be hashed)
     * @return bool True if successful, false otherwise
     */
    public function createUser($username, $email, $password) {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                'INSERT INTO Users (username, email, password_hash, is_active, is_admin) VALUES (:username, :email, :password_hash, 1, 0)'
            );
            return $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verify user password
     * @param string $password Plain text password
     * @param string $hash Stored password hash
     * @return bool True if password matches, false otherwise
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash) || hash_equals($hash, $password);
    }

    /**
     * Update user password
     * @param int $userId User ID
     * @param string $newPassword New plain text password
     * @return bool True if successful, false otherwise
     */
    public function updatePassword($userId, $newPassword) {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare(
                'UPDATE Users SET password_hash = :password_hash WHERE user_id = :user_id'
            );
            return $stmt->execute([
                'password_hash' => $passwordHash,
                'user_id' => $userId,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update user profile
     * @param int $userId User ID
     * @param string $email Email address
     * @param string $username Username
     * @return bool True if successful, false otherwise
     */
    public function updateProfile($userId, $email, $username) {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE Users SET email = :email, username = :username WHERE user_id = :user_id'
            );
            return $stmt->execute([
                'email' => $email,
                'username' => $username,
                'user_id' => $userId,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deactivate user account
     * @param int $userId User ID
     * @return bool True if successful, false otherwise
     */
    public function deactivateUser($userId) {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE Users SET is_active = 0 WHERE user_id = :user_id'
            );
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all users (admin function)
     * @return array List of all users
     */
    public function getAllUsers() {
        $stmt = $this->pdo->prepare(
            'SELECT user_id, username, email, is_active, is_admin FROM Users'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete user account
     * @param int $userId User ID
     * @return bool True if successful, false otherwise
     */
    public function deleteUser($userId) {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM Users WHERE user_id = :user_id'
            );
            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Check if username or email is already in use by another user
     * @param string $username Username
     * @param string $email Email
     * @param int $userId Exclude this user ID from the check
     * @return array|null Existing user data or null
     */
    public function checkUserExistsExclude($username, $email, $userId) {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM Users WHERE (username = :username OR email = :email) AND user_id != :user_id LIMIT 1'
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'user_id' => $userId,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get prepared statement object (for advanced queries)
     * @param string $query SQL query
     * @return PDOStatement Prepared statement object
     */
    public function getStatement($query) {
        return $this->pdo->prepare($query);
    }
}
?>
