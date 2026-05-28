# Admin Panel Documentation

## Admin Dashboard Setup

The CineList Admin Panel provides a secure interface for system administrators to manage the application.

### Default Admin Credentials

To access the admin panel, navigate to:
```
http://localhost/moviewatchlist/admin/login.php
```

**Important: Change the default credentials immediately after first login!**

Edit the file `/admin/includes/admin_config.php` to change:
- `ADMIN_USERNAME` - Default: "admin"
- `ADMIN_PASSWORD` - Default: "SecureAdminPass123!"
- `ADMIN_EMAIL` - Default: "admin@cinelist.local"

### How to Access the Admin Panel

1. **Direct URL Access**: Navigate directly to `/admin/login.php`
   - Example: `http://localhost/moviewatchlist/admin/login.php`

2. **No visible login button** for normal users - this keeps the admin panel hidden

3. **After login**, you'll be redirected to the admin dashboard at `/admin/dashboard.php`

### Admin Dashboard Features

The admin dashboard provides:
- **Statistics**: View total users, movies, genres, and reviews
- **Recent Users**: See the latest registered users
- **Top Rated Movies**: View movies with the highest ratings
- **Recent Movies**: Monitor recently added movies
- **Quick Links**: Fast access to management pages

### Session Management

- Admin sessions expire after **1 hour** of inactivity
- Session timeout is configured in `/admin/includes/admin_config.php`
- Sessions are automatically refreshed during active use

### Security Features

1. **Hardcoded Credentials**: Admin credentials are stored in code, not database
2. **Hidden Access**: No public navigation to admin login
3. **Session Security**: Time-based session expiration
4. **File Protection**: `.htaccess` prevents direct file access to sensitive files

### Logout

Click the "Logout" button in the admin dashboard header to end your admin session.

### Changing Admin Credentials

Edit `/admin/includes/admin_config.php`:

```php
define('ADMIN_USERNAME', 'your_new_username');
define('ADMIN_EMAIL', 'your_email@domain.com');
define('ADMIN_PASSWORD', 'your_new_secure_password');
```

**Note**: Passwords are stored in plain text in the config file. Use a strong, unique password.

### What Admins Can Do

From the dashboard, admins can access:
- **Manage Movies**: Add, edit, delete movies
- **Manage Genres**: Add, edit, delete genres
- **View System Statistics**: Monitor user and content activity

### Important Security Notes

⚠️ **Keep the admin credentials secure and change them regularly**

⚠️ **Do not share the admin login URL**

⚠️ **Keep the `admin_config.php` file out of version control**

⚠️ **Use HTTPS in production environment**

⚠️ **Regularly review admin access logs**
