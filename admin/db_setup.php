<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASS', 'password');
define('DB_NAME', 'savory_bites');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS ".DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select database
$conn->select_db(DB_NAME);

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        guests INT NOT NULL,
        special_requests TEXT,
        status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        image_path VARCHAR(255),
        is_featured BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        title VARCHAR(100),
        content TEXT NOT NULL,
        rating INT NOT NULL,
        image_path VARCHAR(255),
        is_approved BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS visitors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        page_visited VARCHAR(255) NOT NULL,
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $sql) {
    if ($conn->query($sql) !== TRUE) {
        echo "Error creating table: " . $conn->error;
    }
}

// Create default admin
$password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO admin_users (username, password, email) 
        VALUES ('admin', '$password', 'admin@savorybites.com')";
$conn->query($sql);

$conn->close();
echo "Database setup completed successfully";
?>
