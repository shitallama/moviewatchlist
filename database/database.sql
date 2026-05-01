-- References:
-- Previous project database schema: https://github.com/shitallama/studentcoursehub/blob/main/database/database.sql
-- 1. Drop tables first to prevent errors if you run this multiple times
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS Movies;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS Users;

-- 2. Create independent tables first
CREATE TABLE Users (
    user_id INTEGER PRIMARY KEY AUTO_INCREMENT,    -- Unique identifier for each user
    username VARCHAR(50) NOT NULL UNIQUE,          -- Unique name chosen by the user for login
    email VARCHAR(255) NOT NULL,                   -- User's email address
    password_hash VARCHAR(255) NOT NULL,           -- Encrypted password storage
    is_active TINYINT(1) DEFAULT 1,                -- 1 for active, 0 for deactivated
    is_admin TINYINT(1) DEFAULT 0,                 -- 1 if user has admin privileges, 0 if not
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Date and time of registration
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- 3. Create dependent tables
CREATE TABLE Movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    release_date DATE,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    watched BOOLEAN DEFAULT FALSE,
    watch_date DATE,             -- Added this so your INSERT works
    user_notes TEXT,             -- Added this so your INSERT works
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

CREATE TABLE Review (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT NOT NULL,       -- Added so we know WHICH movie is being reviewed
    user_id INT NOT NULL,        -- Added so we know WHO wrote the review
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text VARCHAR(1000),
    is_recommended BOOLEAN,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES Movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- ==========================================
-- SEED DATA (Inserting the things!)
-- ==========================================

-- Insert Users first
INSERT INTO Users (username, email, password_hash, is_admin) VALUES
('cinephile_99', 'cinephile@example.com', 'hashed_pw_1', 0),
('movie_boss', 'admin@example.com', 'hashed_pw_2', 1);

-- Insert Categories
INSERT INTO categories (name, description) VALUES
('Sci-Fi', 'Science fiction, space, and futuristic concepts.'),
('Action', 'High-energy, stunts, and fast-paced storylines.'),
('Crime', 'True crime and fictional underworld stories.');

-- Insert Movies (Now with the required user_id included)
INSERT INTO Movies (title, genre, rating, watched, watch_date, user_notes, user_id) VALUES
('Inception', 'Sci-Fi', 5, TRUE, '2024-03-15', 'Mind-bending masterpiece, loved the ending.', 1),
('The Dark Knight', 'Action', 5, TRUE, '2024-01-10', 'Best Joker performance ever.', 1),
('Interstellar', 'Sci-Fi', 4, FALSE, NULL, 'Need to watch this on a big screen.', 1),
('Pulp Fiction', 'Crime', 5, TRUE, '2023-11-20', 'Classic Tarantino dialogue.', 2),
('The Matrix Resurrections', 'Sci-Fi', 2, TRUE, '2024-02-05', 'A bit disappointing compared to the original.', 2);

-- Insert some sample Reviews
INSERT INTO Review (movie_id, user_id, rating, review_text, is_recommended) VALUES
(1, 1, 5, 'Absolutely incredible visuals and a plot that keeps you guessing until the very last frame.', TRUE),
(5, 2, 2, 'The nostalgia was nice, but the plot felt really disjointed and unnecessary.', FALSE);

-- Verify the data
SELECT * FROM Movies;
SELECT * FROM Users;
SELECT * FROM categories;
SELECT * FROM Review;
