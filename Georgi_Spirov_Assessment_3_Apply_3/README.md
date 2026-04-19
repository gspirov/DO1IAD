# Project Portfolio – Laravel Web Application

This project is a database-driven web application developed using the Laravel framework. It allows users to browse, search, and manage software projects. Public users can explore projects, while registered users can create, update, and manage their own projects.

The application demonstrates server-side development, database management, and implementation of security best practices.

---

## Requirements

Ensure the following are installed:

- PHP 8.4
- Composer
- Node.js (^22) and npm
- MySQL
- Git

---

## Installation

Clone the repository and install dependencies:

```bash
git clone https://github.com/gspirov/DO1IAD.git
cd Georgi_Spirov_Assessment_3_Apply_3
composer install
npm install
```

Create environment configuration:

```bash
cp .env.example .env
php artisan key:generate
```

---

## Database Setup

The application uses a **secure two-user database setup**:

- **Web user (`web`)**  
  Used by the application during normal operation.  
  Limited to **DML operations**: `SELECT`, `INSERT`, `UPDATE`, `DELETE`.

- **Admin user (`admin`)**  
  Used for database setup and migrations.  
  Has **DML + DDL permissions**: `CREATE`, `ALTER`, `DROP`, etc.

This separation improves security by preventing the application from executing schema-level operations.

### Create Database and Users

Run the provided SQL script:

```sql
mysql -u root -p < create-db.sql
```

**Important:**  
This script must be executed using a MySQL administrator account (e.g. via phpMyAdmin or MySQL CLI), as it creates users and assigns privileges.

---

## Environment Configuration

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=web
DB_PASSWORD=2Yl6OKFNR4e8fLja
```

The project also uses a secondary database connection (`mysql_admin`) for migrations.

Ensure the following variables are set:

```env
DB_ADMIN_USERNAME=admin
DB_ADMIN_PASSWORD=GPKV23Wbwb81oqzi
```

---

## Running Migrations and Seeders

Use the **admin database connection** for schema creation:

```bash
php artisan migrate --database=mysql_admin
php artisan db:seed --database=mysql_admin
```

Or run everything together:

```bash
php artisan migrate --seed --database=mysql_admin
```

To reset the database:

```bash
php artisan migrate:fresh --seed --database=mysql_admin
```

---

## Compiling Front-End Assets

For development:

```bash
npm run dev
```

For production build:

```bash
npm run build
```

---

## Writable Directories

Before running the application, ensure Laravel's writable directories exist and have the correct permissions.

## Linux / macOS

```bash
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R $(whoami):$(id -gn) storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Windows

```bash
mkdir storage\framework\cache\data
mkdir storage\framework\sessions
mkdir storage\framework\views
mkdir storage\logs
type nul > storage\logs\laravel.log
```

---

## Running the Application

Start Laravel’s built-in development server:

```bash
php artisan serve --port=7777
```

Application URL:

```
http://127.0.0.1:7777
```

---

## Typical Setup Workflow

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed --database=mysql_admin
php artisan storage:link
npm run dev
php artisan serve --port=7777
```

---

## Test User

Use the following credentials for evaluation:

```
Email: test@example.com
Password: password
```

---

## Features Overview

### Public Users
- View all projects
- View project details
- Search projects
- Register an account

### Registered Users
- Login and logout
- Create, update, and delete their own projects
- Upload project images
- Comment on projects
- Rate projects
- Add projects to favourites
- Manage profile and avatar

---

## Security Features

The application includes multiple security measures:

- Authentication (login system)
- Authorization (policies restricting user actions)
- Form validation (server-side validation using Form Requests)
- Password hashing
- CSRF protection
- Email verification
- Secure file upload validation
- Protection against SQL injection via Eloquent ORM

---

## Notes

- The application follows the MVC architecture (Model-View-Controller).
- Laravel is used for routing, authentication, validation, and database access.
- The database is pre-populated using seeders for demonstration purposes.
- The system is designed to be modular, maintainable, and secure.

## Testing

The application includes comprehensive feature tests covering core functionality and security.

Run tests:

```bash
php artisan test
```

Covered Areas: 

- Authentication (register, login, password reset)
- Authorization (access control via policies)
- Project management (CRUD)
- File uploads (images, avatars)
- Comments and ratings
- Validation and error handling

Test Environment

- Database is reset before each test using RefreshDatabase
- Isolated test execution (no impact on main DB)
- File storage is mocked using Storage::fake()
