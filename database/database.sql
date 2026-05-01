-- Create the Movies table
DROP TABLE IF EXISTS Movies;
CREATE TABLE Movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    release_date DATE,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    watched BOOLEAN DEFAULT FALSE,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Users(UserID) ON DELETE CASCADE
);
 
-- Sample seed data for Movies (assuming Users exist)
INSERT INTO Movies (title, genre, release_date, rating, watched, user_id) VALUES
('Inception', 'Sci-Fi', '2010-07-16', 5, TRUE, 1),
('The Dark Knight', 'Action', '2008-07-18', 5, TRUE, 1),
('Interstellar', 'Sci-Fi', '2014-11-07', 4, TRUE, 1),
('The Matrix', 'Sci-Fi', '1999-03-31', 5, TRUE, 2),
('Pulp Fiction', 'Crime', '1994-10-14', 5, FALSE, 2),
('The Godfather', 'Drama', '1972-03-24', 5, FALSE, 3),
('Forrest Gump', 'Drama', '1994-07-06', 4, TRUE, 3),
('The Shawshank Redemption', 'Drama', '1994-09-23', 5, TRUE, 4),
('Avatar', 'Sci-Fi', '2009-12-18', 3, FALSE, 4),
('Jurassic Park', 'Adventure', '1993-06-11', 4, TRUE, 5),
('Titanic', 'Romance', '1997-12-19', 4, TRUE, 5),
('Gladiator', 'Action', '2000-05-05', 5, FALSE, 6),
('The Social Network', 'Drama', '2010-10-01', 4, TRUE, 6),
('Harry Potter and the Sorcerer\'s Stone', 'Fantasy', '2001-11-16', 4, TRUE, 7),
('The Lord of the Rings: The Fellowship of the Ring', 'Fantasy', '2001-12-19', 5, TRUE, 7);


CREATE TABLE Review (
    review_id INT PRIMARY KEY,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text VARCHAR(1000),
    is_recommended BOOLEAN NOT NULL,
    created_at DATE NOT NULL,
    updated_at DATE
);