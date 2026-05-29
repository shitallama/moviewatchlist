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
     * Get user by email
     * @param string $email Email address
     * @return array|null User data or null if not found
     */
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM Users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create password reset token
     * @param int $userId User ID
     * @return string Generated reset token
     */
    public function createPasswordResetToken($userId) {
        try {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);

            // Delete any existing reset tokens for this user
            $deleteStmt = $this->pdo->prepare('DELETE FROM password_resets WHERE user_id = :user_id');
            $deleteStmt->execute(['user_id' => $userId]);

            // Insert new reset token
            $insertStmt = $this->pdo->prepare(
                'INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, :expires_at)'
            );
            $insertStmt->execute([
                'user_id' => $userId,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt,
            ]);

            return $token;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Verify password reset token
     * @param string $token Reset token
     * @return array|null Token data with user_id or null if invalid/expired
     */
    public function verifyResetToken($token) {
        try {
            $tokenHash = hash('sha256', $token);
            $stmt = $this->pdo->prepare(
                'SELECT reset_id, user_id, expires_at, used_at FROM password_resets WHERE token_hash = :token_hash LIMIT 1'
            );
            $stmt->execute(['token_hash' => $tokenHash]);
            $resetRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if token is valid (not used and not expired)
            if (!$resetRow || $resetRow['used_at'] !== null || strtotime($resetRow['expires_at']) < time()) {
                return null;
            }

            return $resetRow;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Reset user password using token
     * @param string $token Reset token
     * @param string $newPassword New plain text password
     * @return bool True if successful, false otherwise
     */
    public function resetPasswordWithToken($token, $newPassword) {
        try {
            // Verify token and get reset data
            $resetData = $this->verifyResetToken($token);
            if (!$resetData) {
                return false;
            }

            $userId = $resetData['user_id'];
            $resetId = $resetData['reset_id'];
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password
            $updateStmt = $this->pdo->prepare(
                'UPDATE Users SET password_hash = :password_hash WHERE user_id = :user_id'
            );
            $updateStmt->execute([
                'password_hash' => $passwordHash,
                'user_id' => $userId,
            ]);

            // Mark the reset token as used
            $markUsedStmt = $this->pdo->prepare('UPDATE password_resets SET used_at = :used_at WHERE reset_id = :reset_id');
            $markUsedStmt->execute([
                'used_at' => date('Y-m-d H:i:s'),
                'reset_id' => $resetId,
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete password reset token
     * @param string $token Reset token
     * @return bool True if successful, false otherwise
     */
    public function deleteResetToken($token) {
        try {
            $tokenHash = hash('sha256', $token);
            $stmt = $this->pdo->prepare('DELETE FROM password_resets WHERE token_hash = :token_hash');
            return $stmt->execute(['token_hash' => $tokenHash]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get prepared statement object (for advanced queries)
     * @param string $query SQL query
     * @return PDOStatement Prepared statement object
     */
    public function getStatement($query) {
        return $this->pdo->prepare($query);
    }

    /**
     * Get reset token details with user email
     * @param string $token Reset token
     * @return array|null Token data with reset_id, user_id, expires_at, used_at, email or null
     */
    public function getResetTokenDetails($token) {
        try {
            $tokenHash = hash('sha256', $token);
            $stmt = $this->pdo->prepare(
                'SELECT pr.reset_id, pr.user_id, pr.expires_at, pr.used_at, u.email '
                . 'FROM password_resets pr '
                . 'JOIN Users u ON u.user_id = pr.user_id '
                . 'WHERE pr.token_hash = :token_hash LIMIT 1'
            );
            $stmt->execute(['token_hash' => $tokenHash]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Deactivate user account (user-initiated)
     * @param int $userId User ID
     * @return bool True if successful, false otherwise
     */
    public function deactivateUserAccount($userId) {
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
     * Reactivate user account (user-initiated)
     * @param int $userId User ID
     * @param string $password User password to verify identity
     * @return bool True if successful, false otherwise
     */
    public function reactivateUserAccount($userId, $password) {
        try {
            // First verify the password
            $stmt = $this->pdo->prepare(
                'SELECT password_hash FROM Users WHERE user_id = :user_id LIMIT 1'
            );
            $stmt->execute(['user_id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user || !password_verify($password, $user['password_hash'])) {
                return false;
            }

            // Reactivate the account
            $updateStmt = $this->pdo->prepare(
                'UPDATE Users SET is_active = 1 WHERE user_id = :user_id'
            );
            return $updateStmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
}
