# Local Development Setup Guide

Follow these steps to run the Casjoe PHP-SaaS Platform on your local machine.

## Prerequisites
- **PHP 8.0+** installed and added to your system PATH.
- **MySQL Server** (or MariaDB) installed and running (e.g., via XAMPP, Laragon, or standalone).

## Setup Steps

### 1. Database Configuration
The default configuration expects:
- Host: `localhost`
- User: `root`
- Password: `` (empty)
- Database: `saas_db` (will be created automatically)

If your local MySQL credentials are different:
1. Create a file named `database.local.php` in the `config/` directory.
2. Return an array with your settings. It will override the defaults.
   ```php
   <?php
   return [
       'username' => 'your_user',
       'password' => 'your_password'
   ];
   ```

### 2. Initialize Environment
Open a terminal in the project root and run:
```bash
php setup_local_env.php
```
This script will:
- Create the database `saas_db`.
- Import the database schema.
- Create a default Tenant (`localhost`).
- Create a default Admin User (`admin@localhost` / `password`).

### 3. Run the Server
Double-click `run_local.bat` or run the following command in your terminal:
```bash
php -S localhost:8000 -t public_html
```

### 4. Access the Application
Open your browser and visit:
[http://localhost:8000](http://localhost:8000)

Login with:
- **Email:** `admin@localhost`
- **Password:** `password`
