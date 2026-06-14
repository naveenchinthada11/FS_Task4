# Full_Stack_Task4
Real-World Full Stack Project
# 📚 Online Bookstore - Complete Real-World Full Stack Application

A professional, production-ready online bookstore application built with PHP, MySQL, and Bootstrap 5. Features include user authentication, shopping cart system, secure checkout, order management, and a comprehensive admin dashboard with analytics.

---

## 🎯 Project Objectives

✅ **Authentication System** - Register, Login, Forgot Password  
✅ **User Dashboard** - View orders and order history  
✅ **Product Management** - Search, filter, and pagination  
✅ **Admin Panel** - Manage books, users, and orders  
✅ **Analytics** - View statistics and order trends  
✅ **Responsive UI** - Modern, clean design with Bootstrap 5  
✅ **Database Schema** - Optimized with foreign keys and indexes  

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Bootstrap 5)
- **Charts**: Chart.js
- **Icons**: Bootstrap Icons

---

## 📁 Project Structure

```
Task4/
├── index.php                          # Homepage with book catalog
├── auth/
│   ├── login.php                     # User login
│   ├── register.php                  # User registration
│   ├── forgot-password.php           # Password recovery
│   └── logout.php                    # Logout functionality
├── dashboard/
│   ├── index.php                     # User dashboard
│   └── order-details.php             # Order details view
├── admin/
│   ├── index.php                     # Admin dashboard
│   ├── manage-books.php              # Book management
│   ├── add-book.php                  # Add new book (also used for edit)
│   ├── edit-book.php                 # Edit existing book
│   ├── manage-users.php              # User management
│   ├── manage-orders.php             # Order management
│   └── analytics.php                 # Analytics & reports
├── includes/
│   ├── config.php                    # Database & app configuration
│   ├── db.php                        # Database connection & helpers
│   ├── functions.php                 # Business logic functions
│   └── navbar.php                    # Navigation component
├── css/
│   └── style.css                     # Global styles
├── js/
│   └── script.js                     # JavaScript utilities
├── uploads/                          # Directory for book covers
├── database.sql                      # SQL database setup script
└── README.md                         # This file
```

---

## 📊 Database Schema

### **Users Table**
Stores user account information
```
- id (PK)
- username (UNIQUE)
- email (UNIQUE)
- password (hashed)
- role (user/admin)
- created_at, updated_at
```

### **Books Table**
Stores product information
```
- id (PK)
- title
- author
- category (indexed)
- price (indexed)
- description
- stock
- cover_image
- created_at, updated_at
- Indexes: category, price
```

### **Orders Table**
Stores order records
```
- id (PK)
- user_id (FK → users)
- total_amount
- status (pending/confirmed/shipped/delivered/cancelled)
- created_at, updated_at
- Indexes: user_id, status
```

### **Order Items Table**
Stores individual items in orders
```
- id (PK)
- order_id (FK → orders)
- book_id (FK → books)
- quantity
- price (price at time of order)
```

---

## 👤 User Roles & Features

### **Regular User**
- ✅ Register and login
- ✅ Browse and search books
- ✅ Filter by category and price
- ✅ View order history
- ✅ View order details

### **Admin User**
- ✅ All user features
- ✅ Dashboard with statistics
- ✅ Add/Edit/Delete books
- ✅ Manage all orders
- ✅ View all users
- ✅ Analytics and reports

---

## 🔐 Authentication Features

### **Registration**
- Username & email validation
- Password strength requirements (min 6 characters)
- Password confirmation
- Duplicate username/email check

### **Login**
- Email-based authentication
- Secure password hashing (bcrypt)
- Session management

### **Forgot Password**
- Email verification (demo mode)
- Password recovery flow

---

## 📖 Core Features

### **1. Homepage & Product Catalog**
### **2. User Dashboard**
### **3. Admin Dashboard**
### **4. Book Management**
### **5. Order Management**
### **6. Analytics**
---

## 🎨 UI/UX Design

### **Color Scheme**
### **Features**
### **Bootstrap 5 Components Used**
---

## 📝 API Functions

### **Authentication Functions**
```php
isLoggedIn()           // Check if user is logged in
isAdmin()              // Check if user is admin
requireLogin()         // Redirect if not logged in
requireAdmin()         // Redirect if not admin
hashPassword()         // Hash password using bcrypt
verifyPassword()       // Verify password against hash
```

### **User Functions**
```php
getCurrentUser()       // Get current logged-in user
getUserById($id)       // Get user by ID
getAllUsers()          // Get all users
```

### **Book Functions**
```php
getBooks($page, $limit, $search, $category, $sort)  // Get paginated books
getBookById($id)       // Get single book
getTotalBooks($search, $category)                     // Count books
addBook()              // Create new book
updateBook()           // Update book details
deleteBook()           // Delete book
getCategories()        // Get all categories
```

### **Order Functions**
```php
getUserOrders($user_id)           // Get user's orders
getOrderWithItems($order_id)      // Get order with items
createOrder($user_id, $items)     // Create new order
updateOrderStatus($order_id, $status)  // Update status
```

### **Analytics Functions**
```php
getStatistics()        // Get overall stats
getOrdersPerDay()      // Get 7-day order data
```

---

### **Sample Books**
- The Great Gatsby
- To Kill a Mockingbird
- 1984
- Sapiens
- Atomic Habits

---

## 📋 CRUD Operations

### **Create**
- Add new books (Admin only)
- Register new users
- Create orders

### **Read**
- View all books with pagination
- Search and filter books
- View user orders
- View admin statistics

### **Update**
- Edit book details (Admin only)
- Update order status (Admin only)
- Change password

### **Delete**
- Delete books (Admin only)
- Delete users (Admin only)

---



## 📚 What to Do in My Admin PHP

### **My Admin PHP Functions & Features**

The Admin Panel (`admin/` folder) contains all administrative functionality:

#### **1. Dashboard (admin/index.php)**
#### **2. Manage Books (admin/manage-books.php)**
#### **3. Add/Edit Book (admin/add-book.php & admin/edit-book.php)**
#### **4. Manage Orders (admin/manage-orders.php)**
#### **5. Manage Users (admin/manage-users.php)**
#### **6. Analytics (admin/analytics.php)**
---
## ✨ Features Summary

| Feature | User | Admin |
|---------|------|-------|
| Browse Books | ✅ | ✅ |
| Search & Filter | ✅ | ✅ |
| View Dashboard | ✅ | ✅ |
| View Orders | ✅ | ✅ |
| Add Books | ❌ | ✅ |
| Edit Books | ❌ | ✅ |
| Delete Books | ❌ | ✅ |
| Manage Users | ❌ | ✅ |
| View Analytics | ❌ | ✅ |
| Update Order Status | ❌ | ✅ |

---

