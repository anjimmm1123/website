-- Create database
CREATE DATABASE IF NOT EXISTS stmik_enterprise 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE stmik_enterprise;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_username (username),
    UNIQUE KEY uk_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resources table
CREATE TABLE IF NOT EXISTS resources (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255),
    created_by BIGINT UNSIGNED,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order),
    CONSTRAINT fk_resources_created_by FOREIGN KEY (created_by) 
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id BIGINT UNSIGNED,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_parent_id (parent_id),
    INDEX idx_display_order (display_order),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) 
        REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resource Categories (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS resource_categories (
    resource_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (resource_id, category_id),
    CONSTRAINT fk_resource_categories_resource FOREIGN KEY (resource_id) 
        REFERENCES resources(id) ON DELETE CASCADE,
    CONSTRAINT fk_resource_categories_category FOREIGN KEY (category_id) 
        REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Access Logs
CREATE TABLE IF NOT EXISTS access_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    resource_id BIGINT UNSIGNED,
    action VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_resource_id (resource_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_access_logs_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_access_logs_resource FOREIGN KEY (resource_id) 
        REFERENCES resources(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pages table for CMS
CREATE TABLE IF NOT EXISTS pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    meta_description TEXT,
    slug VARCHAR(255) NOT NULL,
    language VARCHAR(10) DEFAULT 'en',
    status ENUM('published', 'draft') NOT NULL DEFAULT 'draft',
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_slug_language (slug, language),
    INDEX idx_status (status),
    INDEX idx_language (language),
    CONSTRAINT fk_pages_created_by FOREIGN KEY (created_by) 
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Security Logs
CREATE TABLE IF NOT EXISTS security_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED,
    ip_address VARCHAR(45),
    user_agent TEXT,
    details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_event (event),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    CONSTRAINT fk_security_logs_user FOREIGN KEY (user_id) 
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@bima.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert default pages
INSERT INTO pages (title, content, meta_description, slug, language, status) VALUES
('Home', '<h1>Welcome to Bima System</h1><p>This is the home page.</p>', 'Welcome to Bima System - Your trusted partner', 'home', 'en', 'published'),
('About', '<h1>About Us</h1><p>Learn more about our company.</p>', 'About Bima System - Our story and mission', 'about', 'en', 'published'),
('Contact', '<h1>Contact Us</h1><p>Get in touch with our team.</p>', 'Contact Bima System - We are here to help', 'contact', 'en', 'published'); 