-- Create database if not exists
CREATE DATABASE IF NOT EXISTS stmik_enterprise 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE stmik_enterprise;

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table
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

-- Create password_resets table
CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) 
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blog_posts table
CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id BIGINT UNSIGNED NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    UNIQUE KEY uk_slug (slug),
    INDEX idx_status (status),
    INDEX idx_author (author_id),
    INDEX idx_published_at (published_at),
    CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id) 
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create services table
CREATE TABLE IF NOT EXISTS services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-laptop-code',
    link VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    position VARCHAR(100),
    testimonial TEXT NOT NULL,
    image VARCHAR(255),
    rating TINYINT UNSIGNED DEFAULT 5,
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create portfolio table
CREATE TABLE IF NOT EXISTS portfolio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    client VARCHAR(100),
    completion_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create pages table
CREATE TABLE IF NOT EXISTS pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT,
    meta_description VARCHAR(255),
    status ENUM('published', 'draft') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create settings table
CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create logs table
CREATE TABLE IF NOT EXISTS logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level ENUM('info', 'warning', 'error') NOT NULL,
    message TEXT NOT NULL,
    context JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_level (level),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create gallery table
CREATE TABLE IF NOT EXISTS gallery (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create about_page table
CREATE TABLE IF NOT EXISTS about_page (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    years_experience INT UNSIGNED DEFAULT 10,
    students_count INT UNSIGNED DEFAULT 1000,
    graduates_count INT UNSIGNED DEFAULT 5000,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contact_info table
CREATE TABLE IF NOT EXISTS contact_info (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    instagram_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@bima.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewYpR1IOBYyGqKHy', 'System Administrator', 'admin');

-- Insert default pages
INSERT INTO pages (title, slug, content, meta_description, status) VALUES
('Beranda', 'home', '<h1>Selamat Datang di STMIK Enterprise</h1><p>Kampus Teknologi Informasi Terkemuka di Indonesia</p>', 'STMIK Enterprise - Kampus Teknologi Informasi Terkemuka', 'published'),
('Tentang', 'about', '<h1>Tentang Kami</h1><p>Pelajari lebih lanjut tentang sejarah, visi, dan misi kampus kami.</p>', 'Tentang STMIK Enterprise - Sejarah, Visi, dan Misi', 'published'),
('Akademik', 'academic', '<h1>Program Akademik</h1><p>Program studi dan kurikulum yang kami tawarkan.</p>', 'Program Akademik STMIK Enterprise - Program Studi dan Kurikulum', 'published'),
('Kontak', 'contact', '<h1>Hubungi Kami</h1><p>Informasi kontak dan lokasi kampus kami.</p>', 'Kontak STMIK Enterprise - Informasi dan Lokasi Kampus', 'published');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'STMIK Enterprise'),
('site_description', 'Kampus Teknologi Informasi Terkemuka'),
('contact_email', 'info@stmik-enterprise.ac.id'),
('contact_phone', '+62 21 5555 1234'),
('contact_address', 'Jl. Pendidikan No. 123, Jakarta Selatan, DKI Jakarta 12345'),
('social_facebook', 'https://facebook.com/stmikenterprise'),
('social_twitter', 'https://twitter.com/stmikenterprise'),
('social_linkedin', 'https://linkedin.com/company/stmik-enterprise'),
('social_instagram', 'https://instagram.com/stmikenterprise'),
('social_youtube', 'https://youtube.com/stmikenterprise');

-- Insert sample data for services
INSERT INTO services (title, description, icon, link, display_order) VALUES
('Program Studi', 'Kami menawarkan berbagai program studi teknologi informasi yang komprehensif dan terakreditasi.', 'fas fa-graduation-cap', '#', 1),
('Fasilitas Kampus', 'Fasilitas modern dan lengkap untuk mendukung proses pembelajaran mahasiswa.', 'fas fa-building', '#', 2),
('Penelitian', 'Program penelitian inovatif dalam bidang teknologi informasi dan komputer.', 'fas fa-flask', '#', 3),
('Pengabdian Masyarakat', 'Program pengabdian masyarakat dalam bidang teknologi informasi.', 'fas fa-hands-helping', '#', 4),
('Kemitraan', 'Kerjasama dengan berbagai institusi dan perusahaan teknologi terkemuka.', 'fas fa-handshake', '#', 5),
('Karir & Alumni', 'Program pengembangan karir dan jaringan alumni yang kuat.', 'fas fa-users', '#', 6);

-- Insert sample data for gallery
INSERT INTO gallery (title, description, image_url, display_order) VALUES
('Gedung Kampus', 'Gedung kampus modern kami di pusat kota', '/frontend/assets/images/gallery/campus.jpg', 1),
('Laboratorium Komputer', 'Laboratorium komputer berteknologi tinggi untuk mahasiswa', '/frontend/assets/images/gallery/lab.jpg', 2),
('Kegiatan Mahasiswa', 'Mahasiswa berpartisipasi dalam berbagai kegiatan', '/frontend/assets/images/gallery/activities.jpg', 3),
('Wisuda', 'Perayaan wisuda tahunan', '/frontend/assets/images/gallery/graduation.jpg', 4),
('Pusat Penelitian', 'Pusat penelitian dan pengembangan kami', '/frontend/assets/images/gallery/research.jpg', 5),
('Proyek Mahasiswa', 'Pameran proyek inovatif mahasiswa', '/frontend/assets/images/gallery/projects.jpg', 6);

-- Insert sample data for about_page
INSERT INTO about_page (description, years_experience, students_count, graduates_count, image_url) VALUES
('STMIK Enterprise adalah perguruan tinggi teknologi informasi terkemuka yang berdedikasi untuk menghasilkan lulusan yang kompeten dan siap bersaing di era digital. Dengan kurikulum yang selalu diperbarui sesuai perkembangan teknologi, fasilitas modern, dan dosen-dosen yang berpengalaman, kami mempersiapkan mahasiswa untuk menjadi profesional IT yang handal. Komitmen kami terhadap kualitas pendidikan dan inovasi telah menjadikan kami salah satu perguruan tinggi teknologi informasi terbaik di Indonesia.', 
15, 15000, 75000, '/frontend/assets/images/about.jpg');

-- Insert sample data for contact_info
INSERT INTO contact_info (address, phone, email, facebook_url, twitter_url, linkedin_url, instagram_url) VALUES
('123 Education Street, Academic District, Jakarta 12345', 
'+62 21 5555 1234', 
'info@stmik-enterprise.ac.id', 
'https://facebook.com/stmikenterprise', 
'https://twitter.com/stmikenterprise', 
'https://linkedin.com/company/stmik-enterprise', 
'https://instagram.com/stmikenterprise'); 