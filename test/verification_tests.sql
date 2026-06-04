-- 1. DATABASE SCHEMA CREATION
CREATE TABLE Users (
    user_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
    reset_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY token_hash_unique (token_hash),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- 2. ROBUSTNESS & BOUNDARY TESTING SCRIPTS
SET sql_mode = 'STRICT_ALL_TABLES';

-- Username Tests [cite: 67]
INSERT INTO Users (username, email, password_hash) VALUES ('', 'test1@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('a', 'test2@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test3@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'test4@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES (NULL, 'test5@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('duplicate_user', 'test6@test.com', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('duplicate_user', 'test7@test.com', 'hashed_pw');

-- Email Tests [cite: 68]
INSERT INTO Users (username, email, password_hash) VALUES ('user_email_1', '', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('user_email_2', 'a@b.c', 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('user_email_3', REPEAT('a', 255), 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('user_email_4', REPEAT('a', 256), 'hashed_pw');
INSERT INTO Users (username, email, password_hash) VALUES ('user_email_5', NULL, 'hashed_pw');

-- Password Hash Tests [cite: 69]
INSERT INTO Users (username, email, password_hash) VALUES ('user_pw_1', 'pw1@test.com', '');
INSERT INTO Users (username, email, password_hash) VALUES ('user_pw_2', 'pw2@test.com', 'a');
INSERT INTO Users (username, email, password_hash) VALUES ('user_pw_3', 'pw3@test.com', REPEAT('a', 255));
INSERT INTO Users (username, email, password_hash) VALUES ('user_pw_4', 'pw4@test.com', REPEAT('a', 256));
INSERT INTO Users (username, email, password_hash) VALUES ('user_pw_5', 'pw5@test.com', NULL);

-- Account Status & Role Tests [cite: 70, 71]
INSERT INTO Users (username, email, password_hash, is_active) VALUES ('user_active_1', 'act1@test.com', 'hash', 0);
INSERT INTO Users (username, email, password_hash, is_active) VALUES ('user_active_4', 'act4@test.com', 'hash', 'not_a_number');
INSERT INTO Users (username, email, password_hash, is_admin) VALUES ('user_admin_4', 'adm4@test.com', 'hash', 'not_a_number');

-- Password Reset Relational Integrity Tests [cite: 73, 74, 75]
INSERT INTO Users (user_id, username, email, password_hash) VALUES (999, 'reset_test_user', 'reset@test.com', 'hash');
INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (999, 'valid_token_1', '2026-12-31 23:59:59');
INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (NULL, 'valid_token_2', '2026-12-31 23:59:59');
INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES ('not_a_number', 'valid_token_3', '2026-12-31 23:59:59');
INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (888888, 'valid_token_4', '2026-12-31 23:59:59');