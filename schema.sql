-- Create database (run this once in phpMyAdmin)
CREATE DATABASE IF NOT EXISTS sonali_makeup CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sonali_makeup;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username sahil, password 2005
INSERT INTO admins (username, password_hash)
VALUES (
    'sahil',
    -- password: 2005
    '$2y$10$0xq9l/z4NRxz5KNCopwH6u7Lp4/6aGBoZoIJTQcE9yP2bKLEu3bV2'
)
ON DUPLICATE KEY UPDATE username = username;

-- Services table (cards on home page)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0
);

-- Seed default services (admin can edit later)
INSERT INTO services (title, description, image_url, sort_order)
VALUES
('Bridal Looks', 'Timeless, camera-ready bridal makeup tailored to your features and outfit.', 'images/bridal.jpg', 1),
('Makeup Classes (Limited Seats)', 'Hands-on professional makeup classes for beginners and aspiring artists.', 'images/classes.jpg', 2),
('Nail Design', 'Creative and elegant nail art that completes your look.', 'images/nails.jpg', 3),
('Groom Makeup', 'Subtle, polished grooming for grooms and male guests.', 'images/groom.jpg', 4)
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- Contact settings (editable by admin)
CREATE TABLE IF NOT EXISTS contact_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    headline VARCHAR(150) DEFAULT 'Get in touch with Sonali',
    description TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    address VARCHAR(255),
    instagram VARCHAR(150),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default contact row
INSERT INTO contact_settings (headline, description, phone, email, address, instagram)
VALUES (
    'Let''s create your perfect look',
    'Reach out for bridal bookings, party makeup, or professional classes. Admin can update these details anytime from the dashboard.',
    '+91-00000 00000',
    'contact@sonalimakeupartist.com',
    'Your Studio Address Here',
    'https://instagram.com/yourhandle'
)
ON DUPLICATE KEY UPDATE headline = headline;

-- Users table (front-end form submissions)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120),
    phone VARCHAR(50),
    preferred_service VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site users table (accounts that can log in to view the site)
CREATE TABLE IF NOT EXISTS site_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default demo user: username demo, password demo123
INSERT INTO site_users (username, password_hash)
VALUES (
    'demo',
    '$2y$10$1ioW0OLl8N8odJiYhSYxWe9cYrKtoKfToNCBKx9r17/XJKXxWjIxe'
)
ON DUPLICATE KEY UPDATE username = username;

-- Track user login sessions for admin visibility
CREATE TABLE IF NOT EXISTS user_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES site_users(id) ON DELETE CASCADE
);

