USE traffic_violation_db;

CREATE TABLE IF NOT EXISTS chat_conversations (
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

ALTER TABLE chat_conversations ADD COLUMN IF NOT EXISTS guest_token VARCHAR(64) NULL UNIQUE AFTER guest_phone;

CREATE TABLE IF NOT EXISTS chat_messages (
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
