# Awan Buckle - Retail Point of Sale (POS) & Multi-Branch Inventory System

A high-performance, multi-branch Retail Point of Sale (POS) and Inventory Management System built on Laravel. Designed for retail and wholesale environments with rapid customer checkout, strict dual-store inventory accounting, multi-shop stock transfers, returns handling, and full audit logging.

---

## 🛠️ Technology Stack

### Backend
- **Framework:** [Laravel 8.x](https://laravel.com/) (PHP `^7.3 | ^8.0`)
- **Database Engine:** MySQL / MariaDB with Eloquent ORM
- **Authentication & Security:** Laravel Breeze & Laravel Sanctum
- **Role-Based Access Control (RBAC):** [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission) (`v5.7`)
- **Audit & Activity Tracking:** [Owen-It Laravel-Auditing](https://www.laravel-auditing.com/) (`v13.0`)
- **Server-Side Data Tables:** [Yajra Laravel DataTables](https://yajrabox.com/docs/laravel-datatables) (`v1.5`)
- **Data Import / Export:** [League CSV](https://csv.thephpleague.com/) (`v9.8`)
- **HTTP Client:** GuzzleHTTP (`v7.0`)

### Frontend & UI
- **Template Engine:** Laravel Blade
- **UI Framework & Theme:** Ace Admin Responsive Dashboard Theme (Bootstrap 3 based)
- **Styling:** CSS3, FontAwesome 4.5, Responsive Layouts
- **JavaScript Libraries & Plugins:**
  - **jQuery** & AJAX for asynchronous operations
  - **Yajra / jQuery DataTables** with server-side pagination, searching, and sorting
  - **Select2** & **Chosen** for searchable select inputs
  - **Bootstrap Datepicker & Daterangepicker** for temporal filtering
  - **jbvalidator** for client-side HTML5 form validations
  - **jQuery-Confirm** for interactive confirmation dialogues and modal prompts
  - **printThis.js** for browser receipt printing and thermal invoice generation
  - **Bootstrap Dual Listbox** for managing permissions and multi-select associations
  - **LoadingOverlay** for non-blocking UI loading indicators

### Build Tools & Tooling
- **Asset Bundler:** Laravel Mix 6 / Webpack
- **CSS Preprocessing:** PostCSS
- **Package Managers:** Composer (PHP) & NPM (Node.js)
- **Testing & Environment:** PHPUnit, Laravel Sail

---

## 🚀 Key Features

### 1. 🛒 High-Speed Point of Sale (POS) Terminal
- **Interactive POS Board:** Rapid product search by name, code, or SKU with real-time stock availability display.
- **Cart & Order Parking (On-Hold):** Park active carts to serve other customers and resume saved carts with unique sale keys.
- **Line-Item Price & Discount Controls:** Apply product-level discounts and customized unit pricing directly from the cart.
- **Customer Association:** Seamlessly link walk-in or registered customers to carts for credit, history tracking, and billing.
- **Receipt & Thermal Printing:** Instant printable customer invoices and thermal slips via `printThis.js`.
- **Concurrency & Stock Validation:** Atomic checks prevent selling out-of-stock items across concurrent registers.

### 2. 📦 Dual-Layer Inventory & Ledger Accounting
- **Authoritative Event Ledger (`stock_audits`):** Every stock movement (sale, purchase, return, exchange, adjustment) records an immutable audit ledger entry.
- **Fast-Read Snapshot Cache (`product_available`):** Denormalized, per-shop/per-product stock count for millisecond-latency UI availability checks.
- **Pessimistic Concurrency Locking:** Employs `lockForUpdate()` and database transactions (`DB::transaction`) during checkouts and restocking to eliminate race conditions and stock discrepancies.
- **Negative Stock Protection:** Configurable safeguard preventing stock depletion below zero.

### 3. 🚚 Purchase & Stock Deliveries
- **Inbound Stock Deliveries:** Receive purchase shipments from vendors with detailed bill tracking, batching, and cost price updates.
- **Simple / Quick Purchase Add:** Streamlined interface for rapid inventory receipt without tedious multi-step wizards.
- **Serial Number / Barcode Processing:** Ingest and parse product serial numbers and supporting batch documents.
- **Purchase Returns:**
  - *Bill-Wise Returns:* Traceable returns against original supplier delivery invoices.
  - *Unknown / Walk-in Purchase Returns:* Ad-hoc vendor returns with automated negative stock ledger adjustments.

### 4. 🔄 Multi-Shop & Multi-Branch Management
- **Centralized Multi-Branch Architecture:** Manage inventory across multiple retail outlets, physical shops, and centralized warehouses.
- **Stock Exchange & Inter-Shop Transfers:**
  - Request stock transfers from another branch.
  - Multi-tier workflow (Request &rarr; Approval &rarr; Dispatch &rarr; Receive).
  - Accurate physical stock location tracking (outgoing from source branch, incoming to target branch).
- **Active Shop Context Switching:** Users assigned to multiple branches can seamlessly switch active shop context without logging out.
- **End-of-Day Shop Closing:** Routine for balancing and closing daily shop registers.

### 5. 🔁 Comprehensive Returns System
- **Customer Sale Returns:**
  - *Invoice-Backed Returns:* Return items against original customer orders while preserving historical invoice immutability.
  - *Unknown / Walk-in Sale Returns:* Process returns when customers do not present an original receipt.
- **Shop-Accurate Restocking:** Returned items are physically and logically restored to the branch accepting the return.

### 6. ⚖️ Stock Adjustments
- **Inventory Reconciliation:** Record manual stock increases or decreases due to physical stock count audits, breakage, shrinkage, or expired inventory.
- **Detailed Adjustment Logging:** Track reasons, adjustment dates, affected products, and responsible managers.

### 7. 🏷️ Product Catalog & Taxonomy
- **Flat Retail Catalog UX:** Streamlined for high retail throughput without obstructive category or variant popups during daily cashier workflows.
- **Hierarchical Classification Engine:** 3-tier categorization under the hood (Parent Category &rarr; Sub Category &rarr; Product Category).
- **Units of Measure (UOM):** Manage flexible measurement units (Piece, Dozen, Pack, KG, Meter).
- **Bulk Import via CSV:** Quickly bulk-upload products and SKU catalogs using `league/csv`.

### 8. 👥 Customer & Supplier Management
- **Customer Directory:** Manage customer contact details, order histories, transactions, and search via AJAX Select2.
- **Supplier Ledger:** Complete supplier profiles, delivery records, contact directories, and active status controls.

### 9. 🔐 Role-Based Access Control (RBAC) & Security
- **Granular Permissions:** Powered by `spatie/laravel-permission` with fine-grained control over roles, permissions, and features (e.g., `product.create`, `supplier.read`, `role.status`).
- **Branch-Specific Role Assignments:** Assign roles and shop access on a per-user basis.
- **Audit Logging:** System-wide model audits powered by `owen-it/laravel-auditing` for regulatory compliance and accountability.

### 10. 📊 Analytics & Reporting
- **Sales Analytics:** Filterable sales reports by date ranges, shops, payment methods, and customers.
- **Product Stock Reports:** In-depth product movement reports showing available stock, units sold, and units purchased.
- **Shop-Wise Stock Reports:** Real-time visibility into inventory distribution across all retail branches and warehouses.
- **Project & Issuance Tracking:** Manage organizational assets, project ledgers, and stock issuance history to employees.

---

## 🏗️ Architecture Overview

```
+-------------------------------------------------------------------------------+
|                               Presentation Layer                              |
|   Blade Views  |  Bootstrap 3 (Ace Admin)  |  jQuery  |  Yajra DataTables     |
+-------------------------------------------------------------------------------+
                                        |  HTTP / AJAX
                                        v
+-------------------------------------------------------------------------------+
|                             Application Layer (Laravel)                       |
|   Controllers (POS, Stock, Sale, Return)  |  Middleware (Auth, RBAC)          |
|   Form Requests (Validation)              |  Auditing & Event Listeners       |
+-------------------------------------------------------------------------------+
                                        |
                 +----------------------+----------------------+
                 |                                             |
                 v                                             v
+----------------------------------+          +----------------------------------+
|      Fast-Read Snapshot Cache    |          |    Authoritative Event Ledger    |
|       (product_available)        |          |          (stock_audits)          |
|  - Real-time shop inventory      |          |  - Immutable audit trail         |
|  - Concurrency locked checkouts  |          |  - Purchases, Sales, Returns     |
|  - High-throughput POS reads     |          |  - Inter-shop transfer logs      |
+----------------------------------+          +----------------------------------+
                 |                                             |
                 +----------------------+----------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
|                            Database (MySQL / MariaDB)                         |
|   Transactions (DB::transaction)  |  Pessimistic Locks (lockForUpdate)        |
+-------------------------------------------------------------------------------+
```

---

## 📁 Key Project Structure

```
awan_buckle/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Customer/           # Customer management
│   │   │   ├── Issuance/           # Employee/project stock issuance
│   │   │   ├── Report/             # Sales & inventory reporting
│   │   │   ├── ReturnItem/         # Item returns
│   │   │   ├── Sale/               # POS terminal, cart & sale orders
│   │   │   ├── Shop/               # Multi-shop/branch management
│   │   │   ├── Stock/              # Purchases & stock delivery
│   │   │   ├── StockAdjustment/    # Inventory reconciliations
│   │   │   ├── StockExchange/      # Inter-shop transfer workflows
│   │   │   ├── Supplier/           # Supplier management
│   │   │   └── Web/                # Auth, dashboard, roles & products
│   │   └── Requests/               # Form validation requests
│   └── Models/                     # Eloquent models & audit relationships
├── database/
│   ├── migrations/                 # Schema definitions
│   └── seeders/                    # Initial roles, users & master data
├── public/
│   └── assets/                     # Admin theme, CSS, JS, fonts & plugins
├── resources/
│   └── views/                      # Blade templates and layouts
│       ├── Layout/                 # Master layout, navbar & sidebar
│       ├── sale/                   # POS board, cart & sale views
│       ├── Stock/                  # Deliveries & stock receipts
│       └── stock_exchange/         # Branch transfer views
└── routes/
    └── web.php                     # Authenticated and public routes
```

---

## ⚡ Installation & Setup

### Prerequisites
- **PHP:** `>= 7.3` or `>= 8.0` (with extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `tokenizer`, `xml`, `ctype`, `json`)
- **Composer:** `>= 2.x`
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **Node.js & NPM:** Node.js 14+ / 16+ & NPM

### Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ahmedshabbirawan/awan_buckle.git
   cd awan_buckle
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install and compile frontend assets:**
   ```bash
   npm install
   npm run dev
   ```

4. **Environment Configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Open `.env` and configure your database connection parameters:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=awan_buckle
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Database Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   Navigate to `http://127.0.0.1:8000` in your web browser.

---

## 🧹 Maintenance & Cache Clearing
To clear cached routes, configurations, and compiled views, execute:
```bash
php artisan optimize:clear
```
*(Or access the `/clear` route in a local environment).*

---

## 📄 License
This project is proprietary software developed for Awan Buckle retail and wholesale operations. All rights reserved.
