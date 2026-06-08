# Hofer BrauHaus — Brewery Management System

A Yii2 Advanced Application for managing brewery operations including CRM, products, orders, production, and inventory.

---

## Requirements

- PHP >= 8.0
- MySQL 5.7+ / MariaDB 10.3+
- [Composer](https://getcomposer.org/)
- XAMPP (or any Apache + MySQL stack)

---

## Local Development Setup

### 1. Clone the Repository

```bash
git clone <repo-url> brewery-management-system
cd brewery-management-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Create the Local Database Config

This file is **not committed** to the repository (it contains your local credentials). Copy the example file and edit it:

```bash
cp common/config/main-local.php.example common/config/main-local.php
```

Open `common/config/main-local.php` and update the database credentials to match your local environment:

```php
'dsn'      => 'mysql:host=127.0.0.1;dbname=brewery_management;port=3306',
'username' => 'root',
'password' => '',   // your MySQL password
```

### 4. Create the Database

In MySQL / phpMyAdmin, run:

```sql
CREATE DATABASE brewery_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations

```bash
php yii migrate
```

This creates all the tables (`user`, `customer_company`, `order`, `batch`, etc.).

### 6. Set Up RBAC Roles & Permissions

```bash
php yii rbac/init
```

This creates all roles (`admin`, `manager`, `sales`, `customer`) and assigns their permissions.

> **Optional:** Assign the `admin` role to the first user (ID 1):
> ```bash
> php yii rbac/assign-admin
> ```

---

## Virtual Host Configuration (XAMPP)

The system has two apps — a **backend** (admin panel) and a **frontend** (customer portal).

### Apache — `httpd-vhosts.conf`

Add the following to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
# Backend — Admin Panel
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/brewery-management-system/backend/web"
    ServerName brewery-backend.local
    <Directory "C:/xampp/htdocs/brewery-management-system/backend/web">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Frontend — Customer Portal
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/brewery-management-system/frontend/web"
    ServerName brewery-frontend.local
    <Directory "C:/xampp/htdocs/brewery-management-system/frontend/web">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Windows Hosts File

Add these lines to `C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1  brewery-backend.local
127.0.0.1  brewery-frontend.local
```

### Restart Apache

Restart Apache from the XAMPP Control Panel, then open:

- **Backend:** http://brewery-backend.local
- **Frontend:** http://brewery-frontend.local

---

## Project Structure

```
common/         Shared models, config, and mail templates
backend/        Admin panel (controllers, views, modules)
frontend/       Customer-facing portal
console/        CLI commands and database migrations
vendor/         Composer dependencies (not committed)
```

---

## Available Console Commands

| Command | Description |
|---|---|
| `php yii migrate` | Run all pending database migrations |
| `php yii migrate/down` | Roll back the last migration |
| `php yii rbac/init` | Create all roles and permissions |
| `php yii rbac/assign-admin` | Assign admin role to user ID 1 |

---

## Modules

| Module | Path | Description |
|---|---|---|
| CRM | `backend/modules/crm` | Customer companies, contacts, interactions |
| Product | `backend/modules/product` | Product catalog and categories |
| Order | `backend/modules/order` | Order management |
| Production | `backend/modules/production` | Production orders and batches |
| Inventory | `backend/modules/inventory` | Raw materials and stock transactions |
| Report | `backend/modules/report` | Business reports |
