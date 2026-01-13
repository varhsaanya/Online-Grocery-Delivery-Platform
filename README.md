# Online Grocery Delivery System 

A complete full-stack DBMS project for managing an online grocery delivery system built with PHP, MySQL, HTML, CSS, Bootstrap 5, and JavaScript.

## Features

- **Complete CRUD Operations** for all entities
- **Responsive Bootstrap 5 UI** with modern design
- **Dashboard** with statistics and quick links
- **Modal-based forms** for adding and editing records
- **Foreign key relationships** properly maintained
- **Alert notifications** for success/error messages

## Database Schema

The project includes 16 main entities:
- User
- Addresses
- Vendors
- Stores
- Categories
- Product
- Inventory
- Coupons
- Carts
- CartItem
- Orders
- OrderItem
- Payment
- DeliveryPartners
- OrderDelivery
- Reviews

## Setup Instructions

### Prerequisites
- XAMPP or WAMP installed on your system
- phpMyAdmin access

### Installation Steps

1. **Copy Project Files**
   - Copy all project files to your XAMPP/WAMP `htdocs` folder
   - For XAMPP: `C:\xampp\htdocs\blinkit_dbms\`
   - For WAMP: `C:\wamp64\www\blinkit_dbms\`

2. **Create Database**
   - Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
   - Import the `blinkit_db.sql` file to create the database and all tables
   - Or manually run the SQL script in phpMyAdmin

3. **Configure Database Connection**
   - Open `db.php` file
   - Update database credentials if needed (default: root, no password)
   ```php
   $host = 'localhost';
   $dbname = 'blinkit_db';
   $username = 'root';
   $password = '';
   ```

4. **Access the Application**
   - Start Apache and MySQL services in XAMPP/WAMP
   - Open your browser and navigate to:
     - XAMPP: `http://localhost/blinkit_dbms/`
     - WAMP: `http://localhost/blinkit_dbms/`

## Project Structure

```
blinkit_dbms/
├── blinkit_db.sql          # Database schema file
├── db.php                  # Database connection
├── header.php              # Header with navigation
├── footer.php              # Footer
├── index.php               # Homepage with dashboard
├── users.php               # Users CRUD
├── addresses.php          # Addresses CRUD
├── vendors.php            # Vendors CRUD
├── stores.php             # Stores CRUD
├── categories.php         # Categories CRUD
├── products.php           # Products CRUD
├── inventory.php          # Inventory CRUD
├── coupons.php            # Coupons CRUD
├── carts.php              # Carts CRUD
├── cart_items.php         # Cart Items CRUD
├── orders.php             # Orders CRUD
├── order_items.php        # Order Items CRUD
├── payments.php           # Payments CRUD
├── delivery_partners.php  # Delivery Partners CRUD
├── order_delivery.php     # Order Delivery CRUD
└── reviews.php            # Reviews CRUD
```

## Usage

1. **Dashboard**: The homepage (`index.php`) displays statistics and quick links to all management pages.

2. **Navigation**: Use the navigation bar at the top to access different sections.

3. **CRUD Operations**:
   - **Add**: Click "Add New" button to open a modal form
   - **Edit**: Click "Edit" button on any row to modify records
   - **Delete**: Click "Delete" button to remove records (with confirmation)
   - **View**: All records are displayed in responsive Bootstrap tables

4. **Foreign Key Relationships**:
   - When adding records, dropdown menus show related entities
   - For example, when adding a Product, you select from existing Vendors and Categories

## Features by Page

### Homepage (index.php)
- Total Users count
- Total Products count
- Total Orders count
- Total Delivery Partners count
- Quick links to all management pages

### All CRUD Pages Include:
- Bootstrap 5 responsive tables
- Add/Edit modals
- Delete confirmation modals
- Success/Error alert messages
- Foreign key dropdowns
- Data validation

## Database Relationships

- **User** → **Addresses** (One-to-Many)
- **User** → **Carts** (One-to-Many)
- **User** → **Orders** (One-to-Many)
- **User** → **Reviews** (One-to-Many)
- **Vendors** → **Product** (One-to-Many)
- **Categories** → **Product** (One-to-Many)
- **Categories** → **Categories** (Self-referencing for parent categories)
- **Product** → **Inventory** (One-to-Many)
- **Stores** → **Inventory** (One-to-Many)
- **Stores** → **Orders** (One-to-Many)
- **Carts** → **CartItem** (One-to-Many)
- **Product** → **CartItem** (One-to-Many)
- **Orders** → **OrderItem** (One-to-Many)
- **Orders** → **Payment** (One-to-One)
- **Orders** → **OrderDelivery** (One-to-Many)
- **DeliveryPartners** → **OrderDelivery** (One-to-Many)
- **Product** → **Reviews** (One-to-Many)

## Notes

- All passwords are hashed using PHP's `password_hash()` function
- Timestamps are automatically managed by MySQL
- Foreign key constraints ensure data integrity
- The system uses prepared statements to prevent SQL injection

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **JavaScript**: Vanilla JS (for modal interactions)
- **Icons**: Bootstrap Icons


This project is created for educational purposes.

---

**Developed for DBMS Project - Blinkit Grocery Delivery System**


