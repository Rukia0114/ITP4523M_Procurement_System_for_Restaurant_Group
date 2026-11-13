# Web Application Project

## Overview
A simplified web application designed to manage orders and inventory between Purchase Managers and Suppliers. 

*   **Purchase Manager**: Can create new orders and retrieve existing order records.
*   **Supplier**: Can manage their own items (add, view, delete) and view order details.

## Features & Screenshots

### 1. Order Details (Manager View)
<img width="991" height="767" alt="螢幕擷取畫面 2026-11-13 175925" src="https://github.com/user-attachments/assets/85246920-c51b-45cf-92f7-159d881da88a" />
*Manager can view detailed information regarding orders placed for their company, including item breakdowns and total amounts.*

### 2. Insert Item Information (Supplier View)
<img width="840" height="726" alt="螢幕擷取畫面 2026-11-13 155329" src="https://github.com/user-attachments/assets/46e9e076-ef13-4eb7-bfc4-f155915c6b14" />
*Suppliers can add new items to their inventory, upload an item image, and provide necessary details.*

### 3. Order Creation (Manager View)
<img width="957" height="899" alt="manager" src="https://github.com/user-attachments/assets/03543c05-ac57-4409-9f50-37013901a123" />
*Purchase Managers can search for items, set quantities, add them to the cart, and submit the order.*

---

## Architecture
The application follows an MVC-like structure for organized and maintainable code.

project-web/
├── config/          # Configuration files (database, app settings)
│   ├── database.php # Database credentials and connection settings
│   └── app.php      # Application-wide settings
│
├── core/            # Core classes (singletons, utilities)
│   ├── Auth.php     # Authentication service
│   ├── Database.php # Database connection & prepared statements
│   ├── Validator.php # Input validation logic
│   └── ErrorHandler.php # Centralized error handling
│
├── models/          # Data models (business entities)
│   ├── PurchaseManager.php
│   ├── Supplier.php
│   └── Item.php
│
├── controllers/     # Request handlers (business logic)
│   ├── AuthController.php
│   ├── HomeController.php
│   └── OrderController.php
│
├── views/           # HTML templates
│   └── layouts/     # Page layouts
│
├── bootstrap.php    # Application initialization
└── index_new.php    # Main entry point (rename from index.php)


## Steps
1. Put the project web / PHP file to htdocs
2. Complete the .env
3. Insert the sql file to the xampp MYSQL server
4. Start the Apache server

# pwd and user name can be changed in the sql file 
- Testcase: manager: uid: p001, pwd: p123, supplier: uid: s001, pwd: s123 
