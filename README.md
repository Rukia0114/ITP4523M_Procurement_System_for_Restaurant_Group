## Overview
A simplified procedure to show how the web application will be used
a.	A Purchase Manager can make the orders and retrieve the Order records.
b.	A Supplier can manage it's own items.


## Architecture

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
```



## Steps
1. Put the project web / PHP file to htdocs
2. Complete the .env
3. Insert the sql file to the xampp MYSQL server
4. Start the Apache server

# pwd and user name can be changed in the sql file 
- Testcase: manager: uid: p001, pwd: p123, supplier: uid: s001, pwd: s123 