<p align="center">
  <img src="public/assets/images/shared/greenwich-logo.png" alt="University of Greenwich" width="320">
</p>

<h1 align="center">Greenwich Student Hub</h1>

<p align="center">
  COMP1841 Web Programming 1 Coursework
</p>

---

## Overview

Greenwich Student Hub is a PHP/MySQL discussion platform for coursework-related questions. Students can browse discussions, create posts, upload attachments, reply to other users, manage their own content and mark suitable replies as accepted solutions.

The system also includes profile management, role-based permissions, contact-message storage and an administration area for managing users, modules, discussions, replies and contact messages.

## Main Features

- Public discussion browsing, search and filtering
- User registration and login
- Discussion CRUD
- Module assignment
- Multiple attachments
- Replies and nested replies
- Accepted answers / solved discussions
- Profile and avatar management
- Student, tutor and administrator roles
- Administrator management area
- Contact form with database storage and optional email notification

## Technology

- PHP 8+
- PHP PDO
- MySQL / MariaDB
- HTML5
- Tailwind CSS
- JavaScript
- GSAP
- PrismJS
- PHPMailer
- vlucas/phpdotenv

---

# Tutor / Marker Setup Guide

## 1. Requirements

The following are required:

- PHP 8.0 or newer
- Composer 2
- MySQL or MariaDB
- PHP extensions:
  - `pdo_mysql`
  - `fileinfo`
  - `mbstring`
  - `openssl`

Node.js and npm are **not required to run the submitted project** because the compiled Tailwind CSS file is already included.

## 2. Install PHP dependencies

From the project root, run:

```bash
composer install
composer dump-autoload
```

`composer install` installs the dependencies defined in `composer.json`, including PHPMailer and dotenv.

`composer dump-autoload` rebuilds the Composer autoloader for the project classes.

## 3. Create the environment file

Copy `.env.example` to `.env`.

macOS / Linux:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Then update the database settings:

```env
APP_ENV=development

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uog_discussion_db
DB_USERNAME=root
DB_PASSWORD=""
DB_CHARSET=utf8mb4
```

Email configuration is optional for local marking. The contact message is stored in the database before email delivery is attempted.

> Do not commit real passwords or SMTP credentials to the repository.

## 4. Import the database

Create a database named:

```text
uog_discussion_db
```

Then import:

```text
database/uog_discussion_db.sql
```

Using the MySQL command line:

```bash
mysql -u root -p uog_discussion_db < database/uog_discussion_db.sql
```

The SQL file can also be imported through phpMyAdmin.

## 5. Start the application

From the project root:

```bash
php -S localhost:8000 -t public
```

Then open:

```text
http://localhost:8000
```

## 6. Accessing the system

Public pages can be viewed without logging in.

A normal student account can be created through:

```text
/register
```

Administrator credentials, if required for marking, should be taken from the coursework report / submission details rather than stored publicly in this repository.

The administration area is available at:

```text
/admin
```

---

## Useful Routes

| Route | Purpose |
|---|---|
| `/` | Home page |
| `/discussions` | Discussion list |
| `/discussions/create` | Create discussion |
| `/login` | Login |
| `/register` | Registration |
| `/profile` | User profile |
| `/profile/questions` | User discussions |
| `/profile/preferences` | Account preferences |
| `/contact` | Contact form |
| `/admin` | Administration area |

## Project Structure

```text
COMP1841/
├── app/
│   ├── Controllers/
│   ├── Core/
│   ├── Helpers/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   └── Views/
├── config/
├── database/
│   └── uog_discussion_db.sql
├── public/
│   ├── assets/
│   ├── uploads/
│   └── index.php
├── resources/
├── .env.example
├── composer.json
├── package.json
└── export-db.php
```

## Optional Frontend Rebuild

The compiled stylesheet is already included. Rebuilding is only necessary when modifying Tailwind styles.

```bash
npm install
npm run build
```

## Security Notes

The project includes:

- Password hashing
- PDO prepared statements
- CSRF protection
- Server-side validation
- Role-based permissions
- Ownership checks
- Upload MIME-type and extension validation
- Environment-based configuration

The `.env` file should remain outside version control.

## Academic Notice

This repository was created for the University of Greenwich COMP1841 Web Programming 1 coursework.
