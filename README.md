# CineList - Movie Watchlist Application

A modern, responsive web application for managing your personal movie watchlist, reviews, and genre categories.

## Features

- 🎬 **Movie Management**: Add, edit, and delete movies from your collection
- ⭐ **Review System**: Write and share reviews for movies you've watched
- 📑 **Watch Status Tracking**: Track movies as watched, plan to watch, or currently watching
- 🎭 **Genre Management**: Organize movies by custom genres
- 👤 **User Profiles**: Manage personal profiles and preferences
- 🔐 **User Authentication**: Secure login and registration system
- 👨‍💼 **Admin Dashboard**: Administrative controls for managing users and content
- 📱 **Responsive Design**: Works seamlessly on desktop and mobile devices
- 🌓 **Light/Dark Mode**: Optimized for both light and dark themes

## Project Structure

```
├── admin/                    # Admin dashboard and management
├── assets/                   # CSS styles and JavaScript files
├── database/                 # Database schema and configuration
├── genres_management/        # Genre CRUD operations
├── includes/                 # Shared utilities and database connection
├── Login/                    # Authentication pages
├── movie_management/         # Movie CRUD operations
├── review_system/            # Review and rating functionality
├── watch_status_management/  # Watchlist status tracking
└── [root files]             # Main application pages
```

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Architecture**: MVC-inspired with repositories and managers

## Installation

1. Place files in your web server directory (e.g., `/xampp/htdocs/moviewatchlist`)
2. Import database schema from `database/database.sql`
3. Configure database connection in `includes/db.php`
4. Access the application through your web server

## Getting Started

- Main page: `index.php`
- Register: `Login/register.php`
- Login: `Login/login.php`

## Admin Panel

- **Admin Dashboard**: `http://localhost/moviewatchlist/admin/login.php`
- **Manage Users**: Add, edit, and remove user accounts
- **Manage Reviews**: Moderate and manage user reviews
- **Site Administration**: Full control over content and users
