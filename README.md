<div align="center">

# 🛍️ Innov Ecom
### PHP & MySQL E-Commerce Web Application

**An e-commerce and business website with a product catalog, shopping workflows, account management, order processing, and administration modules.**

[![PHP](https://img.shields.io/badge/PHP-Core%20PHP-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Frontend-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![GitHub](https://img.shields.io/badge/Source-GitHub-181717?logo=github)](https://github.com/AdityaGit8009/innov-ecom)

[**Explore Source Code**](https://github.com/AdityaGit8009/innov-ecom) · [**Report an Issue**](https://github.com/AdityaGit8009/innov-ecom/issues) · [**Developer**](https://github.com/AdityaGit8009)

</div>

---

## 📖 Overview

**Innov Ecom** is a PHP-based e-commerce web project that combines customer-facing shopping pages with administrative and business-oriented sections. The repository contains dedicated pages and handlers for product discovery, carts, wishlists, user accounts, order placement, payment initiation, shipping estimates, invoices, and order history.

The site also contains company, service, training, and project-related pages, making it more than a standalone product storefront.

> **Project status:** This README documents modules and entry points observed in the source repository. The presence of a file or integration does not guarantee that the full flow has been tested end-to-end or is production-ready.

## ✨ Key Modules

| Area | Repository entry points / capabilities |
| --- | --- |
| 🛒 Shopping | Product listing (`products.php`), product details (`product.php`), cart (`cart.php`), cart updates and removal |
| ❤️ Discovery | Wishlist, product comparison, recently viewed products, stock checks |
| 👤 Accounts | Registration, login, logout, user profile, address management |
| 📦 Orders | Checkout, order placement, order history, order details, cancellation, and tracking |
| 💳 Payments | Razorpay order-creation integration entry point (`razorpay_create_order.php`) |
| 🏷️ Discounts | Coupon application and coupon removal handlers |
| 🚚 Delivery | Delivery quote API endpoint (`api_delivery_quote.php`) |
| 🧾 Documents | Invoice page (`invoice.php`) |
| ⭐ Reviews | Review submission and review reaction handlers |
| 🧑‍💼 Administration | Dedicated `admin/` directory |
| 🏢 Business content | About, services, technology, training, projects, and contact pages |

### Customer journey

```mermaid
flowchart LR
    A[Browse Products] --> B[View Product]
    B --> C[Cart / Wishlist]
    C --> D[Checkout]
    D --> E[Place Order]
    E --> F[Order Details / Tracking]
```

## 🧰 Technology Stack

| Layer | Technology |
| --- | --- |
| Server-side | Core PHP |
| Database access | PHP MySQLi |
| Database | MySQL / MariaDB-compatible configuration |
| Frontend | HTML, CSS, JavaScript |
| Dynamic requests | PHP AJAX endpoints |
| Payment integration | Razorpay-related server endpoint |
| Web server | Apache with PHP (recommended for local setup) |

> Versions and any external JavaScript/CSS dependencies should be checked against your local/server environment. This repository is not a Laravel project.

## 📁 Project Structure

```text
innov-ecom/
├── AJAX/                     # AJAX request handlers
├── admin/                    # Administrative pages and workflows
├── assets/                   # Frontend assets: styles, scripts, images
├── config/                   # Database/application configuration
│   └── db.php                # MySQLi connection bootstrap
├── database/                 # Database-related resources
├── includes/                 # Reusable PHP components/helpers
├── uploads/                  # Uploaded files
├── index.php                 # Home page
├── header.php                # Shared header
├── footer.php                # Shared footer
├── products.php              # Product listing
├── product.php               # Product detail
├── cart.php                  # Shopping cart
├── wishlist.php              # Wishlist
├── compare.php               # Product comparison
├── checkout.php              # Checkout page
├── place_order.php           # Order placement entry point
├── orders.php                # Order listing
├── track_order.php           # Order tracking
├── invoice.php               # Invoice page
├── login.php                 # Login page
├── register.php              # Registration page
├── user_profile.php          # Customer profile
├── manage-addresses.php     # Address management
├── apply_coupon.php         # Apply discounts
├── api_delivery_quote.php   # Delivery quote endpoint
├── razorpay_create_order.php # Razorpay order endpoint
└── ...                       # Additional business and support pages
```

The tree is selective, highlighting principal modules rather than every file.

## 🚀 Getting Started

### Prerequisites

- PHP and Apache (for example, through **XAMPP** on Windows)
- MySQL or MariaDB
- A web browser
- Git (optional, for cloning)

### 1. Clone the repository

```bash
git clone https://github.com/AdityaGit8009/innov-ecom.git
```

### 2. Move it into your web server document root

**Windows / XAMPP example:**

```text
C:\xampp\htdocs\innov-ecom\
```

**Linux / Apache example:**

```text
/var/www/html/innov-ecom/
```

Copy or clone the project there and configure appropriate file ownership/permissions. Never grant the entire project world-writable permissions.

### 3. Create a local database

Use phpMyAdmin or the MySQL CLI:

```sql
CREATE DATABASE spiinnovation
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

The connection code uses `spiinnovation` as its default database name. If the `database/` directory contains the SQL schema or a suitable export, import it into the database; creating an empty database alone is **not sufficient** to populate the application tables.

### 4. Set up the database connection

The existing `config/db.php` supports an optional local configuration file **or** environment variables. Create `config/config.local.php` locally:

```php
<?php
return [
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'spiinnovation',
];
```

Alternatively, configure `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` as environment variables. Avoid committing your local configuration or secrets to Git.

> **Security:** Use a dedicated database account with minimal permissions outside local development. The empty root password shown above is only a common XAMPP-local example, never a production recommendation.

### 5. Start the local server

Start **Apache** and **MySQL** from the XAMPP Control Panel. Open:

```text
http://localhost/innov-ecom/
```

If the application reports a database connection error, recheck the database service, credentials, and imported schema. Additional payment, email, and integration settings may be required for their respective features.

## 💳 Payment Integration

The repository contains `razorpay_create_order.php`, indicating a Razorpay payment-order integration point.

Before testing payments:

1. Review how credentials are loaded and ensure keys are not hardcoded or committed.
2. Use Razorpay **test mode** and sandbox credentials.
3. Review server-side payment verification and order-state transitions.
4. Never mark orders paid solely from a browser callback.
5. Configure HTTPS and secure webhook verification before production use.

**No live payment readiness or successful end-to-end payment test is claimed here.**

## 🔐 Security & Deployment Checklist

Before deploying publicly, review and address the following:

- [ ] Keep database credentials, payment keys, mail credentials, and application secrets out of the public repository.
- [ ] Review authentication, authorization, and administrator access for every sensitive route.
- [ ] Use prepared statements for all database queries and escape output appropriately.
- [ ] Apply CSRF protection to state-changing requests and validate all server inputs.
- [ ] Validate uploaded file types, sizes, storage location, and execution permissions.
- [ ] Verify payment signatures/server-side settlement before recording completed payments.
- [ ] Review coupon, price, stock, order, and shipping calculations on the server.
- [ ] Remove or restrict test, debug, database-update, and maintenance scripts before publishing.
- [ ] Enforce HTTPS and secure session cookie settings.
- [ ] Prevent directory listings and protect configuration and database backup files.
- [ ] Check the privacy implications of any committed content in `uploads/` or database exports.
- [ ] Conduct end-to-end testing of registration, checkout, cancellation, returns, and refunds.

### Deployment notes

For Apache deployment, point the site document root at the intended PHP application directory, enable PHP and the required PHP extensions, and configure your database environment securely. Review the public accessibility of paths including `config/`, `database/`, and `uploads/`.

## 🧪 Suggested Test Scenarios

| Test area | Scenarios |
| --- | --- |
| Accounts | Valid/invalid login, registration validation, session expiry |
| Catalog | Product listings, details, out-of-stock behavior |
| Cart | Add, update quantity, remove, pricing consistency |
| Checkout | Address selection, invalid/empty cart, delivery charges |
| Promotions | Invalid/expired coupons, usage restrictions |
| Payment | Failed, cancelled, duplicate and successful test payments |
| Orders | Order creation, history, cancellation, invoice and tracking |
| Administration | Permission boundaries and protected management actions |

## 🛣️ Improvement Roadmap

These are **suggestions**, not statements that the features are already implemented:

- [ ] Add automated PHP tests and CI checks.
- [ ] Document database schema and provide safe seed/demo data.
- [ ] Introduce a `.env.example` or documented configuration sample.
- [ ] Add screenshots of the storefront, cart, checkout, and admin dashboard.
- [ ] Formalize API endpoint documentation and response formats.
- [ ] Audit and strengthen security before public production use.
- [ ] Add reproducible Docker-based local development.

## 👨‍💻 Developer

**Aditya Pratap Vishwakarma**

- **GitHub:** [@AdityaGit8009](https://github.com/AdityaGit8009)
- **LinkedIn:** [linkedin.com/in/vish-aditya](https://www.linkedin.com/in/vish-aditya/)
- **Email:** [adityapratap7391@gmail.com](mailto:adityapratap7391@gmail.com)

## 🤝 Contributions

Issues and improvement suggestions are welcome. For substantial changes, please open an issue to discuss the proposed work first.

## 📄 License

No license is assumed by this README. Check the repository for an explicit `LICENSE` file before reusing or redistributing the code.

---

<div align="center">

**Built with PHP, MySQL, and a focus on practical e-commerce workflows.**

⭐ If you find the repository useful, consider starring it.

</div>
