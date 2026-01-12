# 📚 Library Management System

A modern, responsive Library Management System built with PHP and MySQL, featuring a beautiful UI with responsive design for all devices.

![Library Management System](https://img.shields.io/badge/Status-Active-brightgreen)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1)
![License](https://img.shields.io/badge/License-MIT-blue)

## ✨ Features

### 🎨 Modern UI/UX
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Beautiful Gradient Theme**: Custom romantic color palette (pink, blue, pale)
- **Interactive Elements**: Hover effects, smooth transitions, and animations
- **Sidebar Navigation**: Collapsible sidebar with active state indicators
- **Mobile-Friendly**: Touch-optimized with hamburger menu for mobile

### 📋 Core Modules

1. **Dashboard**
   - Overview statistics
   - Quick access to all modules
   - Recent activity tracking

2. **Student Management**
   - Add, edit, delete student records
   - Search and filter students
   - Export student data
   - View borrowed book history

3. **Book Management**
   - Complete book catalog management
   - Book status tracking (Available/Borrowed)
   - Soft delete functionality
   - Search by title, code, or notes

4. **Transaction Management**
   - Borrow and return books
   - Transaction history
   - Overdue tracking
   - Filter by status, student, or book
   - Pagination for large datasets

### 🔧 Technical Features
- **Session Management**: Secure user sessions
- **Form Validation**: Client-side and server-side validation
- **Database Security**: Prepared statements to prevent SQL injection
- **Error Handling**: User-friendly error messages
- **Responsive Tables**: Convert to cards on mobile devices
- **Pagination**: Efficient data loading for large datasets
- **Search & Filter**: Advanced search capabilities across modules

## 🚀 Installation

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional)

### Step-by-Step Setup

1. **Clone the Repository**
```bash
   git clone https://github.com/yourusername/library-management-system.git
   cd library-management-system
```

2. **Database Setup**
```sql
   -- Create database
   CREATE DATABASE library_db;

   -- Import database structure
   -- Run the SQL files in /database/migrations/
```

3. **Configuration**
```bash
   # Copy the configuration file
   cp config.php.example config.php

   # Edit config.php with your database credentials
   nano config.php
```

4. **Set Permissions**
```bash
   chmod 755 uploads/
   chmod 644 config.php
```

5. **Access the Application**
```
   http://localhost/library-management-system/
```

## Responsive Design Features
- **Mobile-First Approach**: Optimized for mobile devices
- **Adaptive Layout**: Sidebar collapses on mobile
- **Touch-Friendly**: Large buttons and tap targets
- **Responsive Tables**: Convert to cards on small screens
- **Flexible Grids**: Adaptive card layouts

## Security Features
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: HTML special character encoding
- **Session Management**: Secure session handling
- **Input Validation**: Server-side validation
- **CSRF Protection**: Form token validation

## 🧪 Testing
```bash
# Run basic functionality tests
php -f test_connection.php

# Test database connectivity
php -f test_db.php
```

## 📊 Performance Optimization
- **Database Indexing**: Optimized query performance
- **Pagination**: Reduced page load times
- **Caching**: Session-based caching
- **Minified Assets**: Optimized CSS/JS
- **Lazy Loading**: Images and content

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Tailwind CSS for the CSS framework
- Font Awesome for icons
- PHP for the server-side language
- MySQL for the database

## 📞 Support

For support, email: baikimhoui@gmail.com or create an issue in the GitHub repository.

## 🌟 Future Enhancements

- Email notifications for overdue books
- Barcode scanning for books
- Online book reservation system
- Advanced reporting and analytics
- Multi-language support
- REST API for mobile app
- User authentication and roles
- Bulk import/export functionality

---

Made with ❤️ for library management
