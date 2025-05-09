<?php
/**
 * Database Connection Handler
 * 
 * Provides connection to database with fallback mechanism
 * Supports both MySQL/PostgreSQL and SQLite
 */

// Define database connection parameters
$db_host = getenv('PGHOST') ?: getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('PGDATABASE') ?: getenv('DB_NAME') ?: 'stmik_enterprise';
$db_user = getenv('PGUSER') ?: getenv('DB_USER') ?: 'root';
$db_pass = getenv('PGPASSWORD') ?: getenv('DB_PASS') ?: '';
$db_port = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';

// SQLite database file (as fallback)
$sqlite_file = __DIR__ . '/../../database.sqlite';

// Try connection to main database first
try {
    // Check for DATABASE_URL environment variable (common in cloud environments)
    if (getenv('DATABASE_URL')) {
        $db = new PDO(getenv('DATABASE_URL'));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        // Try PostgreSQL connection first
        try {
            $db = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $pe) {
            // Try MySQL connection next
            try {
                $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $me) {
                // If both PostgreSQL and MySQL fail, use SQLite as fallback
                throw new PDOException("Could not connect to PostgreSQL or MySQL: " . $pe->getMessage() . " | " . $me->getMessage());
            }
        }
    }
} catch (PDOException $e) {
    // Use SQLite as fallback
    error_log("Menggunakan SQLite sebagai database.");
    
    $db = new PDO("sqlite:$sqlite_file");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Initialize SQLite database schema if it doesn't exist
    initSQLiteSchema($db);
}

/**
 * Initialize SQLite database schema
 * 
 * @param PDO $db PDO connection object
 */
function initSQLiteSchema($db) {
    try {
        // Check if tables exist by checking for users table
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        $tableExists = $result->fetch(PDO::FETCH_ASSOC);
        
        if (!$tableExists) {
            // Create essential tables
            
            // Settings table
            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key TEXT UNIQUE NOT NULL,
                value TEXT
            )");
            
            // Users table
            $db->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                full_name TEXT,
                phone TEXT,
                address TEXT,
                profile_image TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Programs table
            $db->exec("CREATE TABLE IF NOT EXISTS programs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT UNIQUE NOT NULL,
                name TEXT NOT NULL,
                description TEXT,
                curriculum TEXT,
                duration TEXT,
                degree TEXT,
                requirements TEXT,
                image_url TEXT,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Gallery table
            $db->exec("CREATE TABLE IF NOT EXISTS gallery (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                image_url TEXT NOT NULL,
                category TEXT DEFAULT 'umum',
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            
            // News table
            $db->exec("CREATE TABLE IF NOT EXISTS news (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                content TEXT NOT NULL,
                excerpt TEXT,
                image_url TEXT,
                author_id INTEGER,
                category TEXT DEFAULT 'umum',
                is_active INTEGER DEFAULT 1,
                view_count INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id)
            )");
            
            // Applications table
            $db->exec("CREATE TABLE IF NOT EXISTS applications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                program_id INTEGER NOT NULL,
                full_name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT NOT NULL,
                address TEXT,
                education TEXT,
                school_name TEXT,
                graduation_year TEXT,
                motivation TEXT,
                status TEXT DEFAULT 'pending',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (program_id) REFERENCES programs(id)
            )");
            
            // Messages table
            $db->exec("CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                subject TEXT,
                message TEXT NOT NULL,
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Insert default admin user
            $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
            $db->exec("INSERT INTO users (username, email, password, role, full_name, is_active) 
                VALUES ('admin', 'admin@stmikenterprise.ac.id', '$defaultPass', 'admin', 'Administrator', 1)");
            
            // Insert default settings
            $defaultSettings = [
                ['site_name', 'STMIK Enterprise'],
                ['site_description', 'Lembaga Pendidikan Tinggi Teknologi Informasi Terkemuka'],
                ['site_email', 'info@stmikenterprise.ac.id'],
                ['site_phone', '+62 123 456 7890'],
                ['site_address', 'Jl. Pendidikan No. 123, Jakarta Selatan, Indonesia']
            ];
            
            $settingStmt = $db->prepare("INSERT INTO settings (key, value) VALUES (?, ?)");
            foreach ($defaultSettings as $setting) {
                $settingStmt->execute($setting);
            }
            
            // Insert sample programs
            $samplePrograms = [
                ['S1-TI', 'Teknik Informatika', 'Program studi yang berfokus pada pengembangan software, data science, dan artificial intelligence.', '8 semester', 'S1', 'frontend/assets/images/ti.jpg'],
                ['S1-SI', 'Sistem Informasi', 'Program studi yang mempelajari analisis, desain, dan pengembangan sistem informasi untuk bisnis dan organisasi.', '8 semester', 'S1', 'frontend/assets/images/si.jpg'],
                ['D3-MI', 'Manajemen Informatika', 'Program diploma yang mempersiapkan ahli di bidang basis data, jaringan, dan administrasi sistem.', '6 semester', 'D3', 'frontend/assets/images/mi.jpg']
            ];
            
            $programStmt = $db->prepare("INSERT INTO programs (code, name, description, duration, degree, image_url, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            foreach ($samplePrograms as $program) {
                $programStmt->execute($program);
            }
            
            error_log("SQLite schema initialized successfully.");
        }
    } catch (PDOException $e) {
        error_log("Error initializing SQLite schema: " . $e->getMessage());
    }
}
?>