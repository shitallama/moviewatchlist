# CineList

CineList is a PHP and MySQL movie watchlist application for tracking a personal movie library, writing reviews, managing genres, and monitoring watch progress. It also includes a separate admin panel for site administration.

## What You Can Do

- Add, edit, view, and delete movies in your personal collection
- Track watch status such as plan to watch, watching, and completed
- Keep notes and ratings for each movie
- Write and browse reviews
- Organize movies by custom genres
- Manage a personal profile and account settings
- Log in, register, reset forgotten passwords, and update credentials
- Use the admin dashboard to manage site data and review content

## Main Features

### Movie management
- Create and maintain a movie collection
- Edit movie metadata such as title, genre, release date, watch date, rating, and notes
- Search and filter movies on the management page

### Watchlist and progress tracking
- Add movies to a watchlist
- Mark movies as watched or unwatched
- Track progress percentages for movies that are in progress
- Update watch status from the watchlist page

### Reviews and ratings
- Add reviews per movie
- View all reviews for a movie
- Display average ratings with star indicators

### Genres
- Add, edit, view, and delete genre entries
- Filter movies by genre

### User account features
- Register a new account
- Log in and out
- Reset forgotten passwords
- Edit profile details and settings

### Admin features
- Dedicated admin login and dashboard
- View application statistics
- Manage users, movies, genres, and reviews

## Project Structure

```text
admin/                     Admin login, dashboard, and management utilities
assets/                    Shared CSS and JavaScript assets
database/                  SQL schema and database setup files
genres_management/         Genre CRUD pages and model
includes/                  Shared PHP includes and database connection
Login/                     Login, logout, and registration pages
movie_management/          Movie CRUD pages and manager class
review_system/             Review pages, handlers, and database helpers
watch_status_management/   Watchlist and watch status pages/services
root pages                 Main site pages such as index, profile, settings, and password reset
```

## Important Pages

- `index.php` - Main landing page
- `profile.php` - User profile page
- `settings.php` - User settings page
- `forgot_password.php` - Start password recovery
- `reset_password.php` - Complete password reset
- `Login/login.php` - User login page
- `Login/register.php` - User registration page
- `movie_management/view_movies.php` - Movie list and management page
- `watch_status_management/watchlist.php` - Personal watchlist page
- `review_system/review_page.php` - Movie review page
- `admin/login.php` - Admin login page
- `admin/dashboard.php` - Admin dashboard

## Requirements

- PHP 7.4 or newer
- MySQL or MariaDB
- Apache or another PHP-compatible web server
- A local environment such as XAMPP, WAMP, or Laragon for development

## Installation

1. Place the project in your web server directory, for example `C:\xampp\htdocs\moviewatchlist`.
2. Start Apache and MySQL in XAMPP or your preferred local stack.
3. Import the database schema from `database/database.sql` into MySQL.
4. Update the database connection settings in `includes/db.php` if needed.
5. Open the application in your browser using the local project URL.

## First Run Checklist

- Import the database successfully
- Confirm the `Movies`, `Users`, `Genres`, `Reviews`, and watch status tables exist
- Verify `includes/db.php` points to the correct database
- Make sure the web server can read the project folder
- Test user registration and login
- Test movie creation and watchlist actions

## Usage Guide

### For regular users

1. Register or log in through the authentication pages.
2. Add movies to your collection.
3. Use the movie management page to edit details or open reviews.
4. Add movies to the watchlist and update progress from the watchlist page.
5. Leave notes, ratings, and reviews for movies you have watched.
6. Update your profile and account settings as needed.

### For admins

1. Go directly to `admin/login.php`.
2. Sign in with the admin credentials configured in `admin/includes/admin_config.php`.
3. Use the dashboard to monitor users, movies, genres, and reviews.
4. Manage content from the admin pages linked in the dashboard.

## Admin Access

The admin panel is intentionally separate from the regular user login flow.

- Admin login URL: `http://localhost/moviewatchlist/admin/login.php`
- Admin dashboard: `http://localhost/moviewatchlist/admin/dashboard.php`
- Admin credentials are defined in `admin/includes/admin_config.php`

Important:
- Change the default admin credentials immediately after first login.
- Keep the admin configuration file secure.
- Use HTTPS in production.

## Database Setup

The application expects a MySQL or MariaDB database with the schema defined in `database/database.sql`.

Typical setup steps:

```sql
-- Import database/database.sql into your MySQL server
```

If you change database credentials, update the connection settings in `includes/db.php`.

## Frontend Assets

- `assets/style.css` - Shared global styles
- `assets/manage.css` - Movie management page styles
- `assets/watchlist.css` - Watchlist page styles
- `assets/login.css` - Login and authentication styles
- `assets/profile.css` - Profile page styles
- `assets/settings.css` - Settings page styles
- `assets/js/` - JavaScript for filtering, updates, and modal behavior

## Backend Layout

- `includes/db.php` - Shared PDO database connection
- `includes/UserManager.php` - User-related data access and operations
- `movie_management/MovieManager.php` - Movie data operations
- `watch_status_management/WatchStatusService.php` - Watchlist logic
- `watch_status_management/repositories/WatchStatusRepository.php` - Watchlist data access
- `review_system/review_db.php` - Review database helpers
- `genres_management/Genre.php` - Genre model and operations

## Security Notes

- Use strong passwords for both users and admin accounts
- Change the default admin credentials right away
- Keep sensitive configuration files out of public sharing
- Validate and sanitize all user input
- Use prepared statements for database access
- Prefer HTTPS in production

## Troubleshooting

### Login does not work
- Confirm the database connection in `includes/db.php`
- Verify the user exists in the database
- Check that PHP sessions are enabled

### Movies are not saving
- Confirm the database schema was imported completely
- Check for missing required fields
- Review the browser and server errors

### Admin login fails
- Make sure you are using the credentials from `admin/includes/admin_config.php`
- Verify the admin URL is correct
- Confirm the admin session files are being written correctly

### Styles or buttons look broken
- Check that the CSS files in `assets/` are loading
- Clear the browser cache
- Confirm there are no PHP errors before the page markup

## Development Notes

- The codebase uses plain PHP with modular files rather than a full framework
- Data access is split across manager, service, and repository-style classes
- Frontend behavior is implemented with small JavaScript helpers in `assets/js/`

## Suggested Next Improvements

- Add database migrations or installation scripts
- Add pagination for large lists
- Add role-based permissions for more admin actions
- Add audit logs for destructive actions
- Add automated tests for core flows

## License

No license file is currently included. Add one if you want to publish or share the project formally.
