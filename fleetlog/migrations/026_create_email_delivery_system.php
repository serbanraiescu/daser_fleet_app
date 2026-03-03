<?php
/**
 * Migration 026: Create Email Delivery System Tables
 */
return [
    'up' => "
        CREATE TABLE IF NOT EXISTS email_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            body_html LONGTEXT,
            body_text LONGTEXT,
            attempts INT DEFAULT 0,
            status ENUM('pending', 'processing', 'sent', 'failed') DEFAULT 'pending',
            error_message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            scheduled_at TIMESTAMP NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS email_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            status ENUM('success', 'failed') NOT NULL,
            error_message TEXT,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Add provider_response if it doesn't exist
        SET @dbname = DATABASE();
        SET @tablename = 'email_logs';
        SET @columnname = 'provider_response';
        SET @preparedStatement = (SELECT IF(
          (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
          'SELECT 1',
          'ALTER TABLE email_logs ADD COLUMN provider_response TEXT'
        ));
        PREPARE stmt FROM @preparedStatement;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    ",
    'down' => "
        DROP TABLE IF EXISTS email_queue;
    "
];
