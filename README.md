# 🏢 ERP_Project

> A comprehensive Enterprise Resource Planning (ERP) web application built with PHP Laravel.

---

## ⚠️ Current Status

> **⛔ DATABASE ERROR** — The application is currently experiencing a database connectivity issue.
> Please do not deploy to production until this is resolved.
> See [Known Issues](#known-issues) for details.

---

## 📋 Table of Contents

- [About](#about)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Features](#features)
- [Known Issues](#known-issues)
- [Contributing](#contributing)
- [License](#license)

---

## About

**ERP_Project** is a full-featured enterprise resource planning system designed to streamline business operations including inventory management, HR, finance, and reporting — all in one unified platform.

---

## 🛠 Tech Stack

| Layer      | Technology          |
|------------|---------------------|
| Backend    | PHP 8.x / Laravel 10 |
| Frontend   | Blade Templates, Bootstrap |
| Database   | MySQL / PostgreSQL   |
| Auth       | Laravel Sanctum / Breeze |
| Queue      | Laravel Queue (Redis/DB) |
| Server     | Apache / Nginx       |

---

## ✅ Requirements

- PHP >= 8.1
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL >= 8.0 or PostgreSQL >= 14
- Laravel >= 10.x

---

## 🚀 Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/erp_project.git
cd erp_project

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations
php artisan migrate --seed

# 7. Start the development server
php artisan serve
```

---

## ⚙️ Configuration

Update your `.env` file with the correct values:

```env
APP_NAME=ERP_Project
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_project
DB_USERNAME=root
DB_PASSWORD=your_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 📦 Features

- 👥 **Human Resources** — Employee management, attendance, payroll
- 📦 **Inventory** — Stock tracking, purchase orders, suppliers
- 💰 **Finance** — Invoicing, expenses, reports
- 📊 **Dashboard** — Real-time KPIs and analytics
- 🔐 **Role-Based Access Control** — Admin, Manager, Staff roles
- 📧 **Notifications** — Email & in-app alerts

---

## 🐛 Known Issues

### ⛔ Database Error (Active)

The application is currently facing a **database connection/migration error**.

**Symptoms:**
- App fails to load or throws `SQLSTATE` / `PDO` exceptions
- Migrations may be incomplete or out of sync

**Temporary Workaround:**
1. Ensure your database server is running
2. Double-check `.env` DB credentials
3. Run `php artisan migrate: fresh --seed` to reset migrations
4. Check `storage/logs/laravel.log` for detailed error traces

> 🔧 Fix in progress — tracking in [Issue #XX](https://github.com/Itzdip190/Erp_project.git)

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -m 'Add your feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Open a Pull Request

---


---

> Built with ❤️ using [Laravel](https://laravel.com)
