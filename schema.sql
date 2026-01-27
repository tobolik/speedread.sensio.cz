-- RSVP Rychločtečka - Database Schema
-- Run this SQL to create the required table

CREATE DATABASE IF NOT EXISTS speedread 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE speedread;

CREATE TABLE IF NOT EXISTS reading_stats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL,
    
    -- Text content
    text_content TEXT NOT NULL,
    text_length INT UNSIGNED NOT NULL COMMENT 'Original text length in characters',
    
    -- Reading statistics
    wpm_achieved INT UNSIGNED NOT NULL COMMENT 'Words per minute achieved',
    total_time_seconds INT UNSIGNED NOT NULL COMMENT 'Total reading time in seconds',
    words_read INT UNSIGNED NOT NULL COMMENT 'Total words read',
    time_saved_seconds INT NOT NULL COMMENT 'Time saved vs 200 WPM (can be negative)',
    current_speed INT UNSIGNED NOT NULL COMMENT 'Speed setting at finish',
    
    -- Session info
    language ENUM('cs', 'en') NOT NULL DEFAULT 'cs',
    is_demo TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Tracking
    referer VARCHAR(2048) NULL,
    ip_hash CHAR(64) NOT NULL COMMENT 'SHA256 hash of IP for privacy',
    
    -- Indexes
    INDEX idx_created_at (created_at),
    INDEX idx_language (language),
    INDEX idx_is_demo (is_demo),
    INDEX idx_ip_hash (ip_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
