-- =============================================
-- TRAFFIC VIOLATION LOOKUP - Database Schema
-- Version: 1.0
-- Engine: InnoDB
-- Charset: utf8mb4
-- =============================================

CREATE DATABASE IF NOT EXISTS traffic_violation_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE traffic_violation_db;

-- =============================================
-- 1. USERS - Người dùng hệ thống
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) NULL,
    status TINYINT NOT NULL DEFAULT 1 COMMENT '1=active, 0=banned',
    reset_token VARCHAR(100) NULL,
    reset_expires DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. OFFENSE CATEGORIES - Danh mục nhóm lỗi
-- =============================================
CREATE TABLE offense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. OFFENSES - Danh sách lỗi vi phạm
-- =============================================
CREATE TABLE offenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên lỗi',
    description TEXT NULL COMMENT 'Mô tả chi tiết',
    penalty VARCHAR(255) NULL COMMENT 'Mức phạt',
    category_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES offense_categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. LOCATIONS - Địa điểm
-- =============================================
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type ENUM('camera','csgt','toll','inspection') NOT NULL,
    address VARCHAR(500) NULL,
    latitude DECIMAL(10,7) NOT NULL DEFAULT 0,
    longitude DECIMAL(10,7) NOT NULL DEFAULT 0,
    description TEXT NULL,
    status TINYINT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. VIOLATIONS - Vi phạm giao thông (BẢNG CHÍNH)
-- =============================================
CREATE TABLE violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20) NOT NULL COMMENT 'Biển số xe',
    vehicle_type ENUM('car','motorcycle','electric_motorcycle') NOT NULL,
    violation_date DATETIME NOT NULL COMMENT 'Thời gian vi phạm',
    location_id INT NULL COMMENT 'Địa điểm vi phạm',
    offense_id INT NULL COMMENT 'Lỗi vi phạm',
    status ENUM('pending','processed','paid') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái xử lý',
    fine_amount VARCHAR(100) NULL COMMENT 'Mức phạt cụ thể',
    decision_number VARCHAR(50) NULL COMMENT 'Số quyết định',
    decision_date DATE NULL COMMENT 'Ngày ra quyết định',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    FOREIGN KEY (offense_id) REFERENCES offenses(id) ON DELETE SET NULL,
    INDEX idx_lookup (plate_number, vehicle_type),
    INDEX idx_plate (plate_number),
    INDEX idx_violation_date (violation_date),
    INDEX idx_status (status),
    INDEX idx_vehicle_type (vehicle_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. NEWS CATEGORIES - Danh mục tin tức
-- =============================================
CREATE TABLE news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. NEWS - Tin tức
-- =============================================
CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    thumbnail VARCHAR(255) NULL,
    category_id INT NULL,
    author_id INT NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    views INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. TRAFFIC SIGN GROUPS - Nhóm biển báo
-- =============================================
CREATE TABLE traffic_sign_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sign_prefix VARCHAR(10) NOT NULL COMMENT 'P, W, R, S',
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. TRAFFIC SIGNS - Biển báo giao thông
-- =============================================
CREATE TABLE traffic_signs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sign_code VARCHAR(20) NOT NULL COMMENT 'P.101, W.201...',
    name VARCHAR(255) NOT NULL,
    group_id INT NULL,
    image VARCHAR(255) NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES traffic_sign_groups(id) ON DELETE SET NULL,
    INDEX idx_code (sign_code),
    INDEX idx_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. FAQS - Câu hỏi thường gặp
-- =============================================
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(500) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    status TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 11. SEARCH HISTORY - Lịch sử tra cứu
-- =============================================
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'NULL nếu là khách',
    plate_number VARCHAR(20) NOT NULL,
    vehicle_type ENUM('car','motorcycle','electric_motorcycle') NOT NULL,
    result_count INT NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    searched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_date (searched_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 12. TRAFFIC ALERTS - Cảnh báo giao thông
-- =============================================
CREATE TABLE traffic_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    alert_type ENUM('accident','congestion','construction','weather','other') NOT NULL DEFAULT 'other',
    expires_at DATETIME NULL,
    created_by INT NULL,
    status TINYINT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_type (alert_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 13. CONTACT MESSAGES - Tin nhắn liên hệ
-- =============================================
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    is_read TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 14. CHAT CONVERSATIONS - Cuộc trò chuyện hỗ trợ
-- =============================================
CREATE TABLE chat_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'NULL nếu là khách chưa đăng nhập',
    guest_name VARCHAR(100) NULL,
    guest_email VARCHAR(100) NULL,
    guest_phone VARCHAR(15) NULL,
    guest_token VARCHAR(64) NULL UNIQUE,
    status ENUM('open','closed') NOT NULL DEFAULT 'open',
    last_message_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_last_message (last_message_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 15. CHAT MESSAGES - Tin nhắn trong cuộc trò chuyện
-- =============================================
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_type ENUM('user','admin') NOT NULL,
    sender_id INT NULL,
    message TEXT NOT NULL,
    is_read TINYINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender_read (sender_type, is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 16. VEHICLES - Phương tiện người dùng
-- =============================================
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plate_number VARCHAR(20) NOT NULL,
    vehicle_type ENUM('car','motorcycle','electric_motorcycle') NOT NULL,
    brand VARCHAR(100) NULL,
    model VARCHAR(100) NULL,
    chassis_number VARCHAR(50) NULL,
    engine_number VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_plate (plate_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
