-- AgriTrust Protocol Database Schema
-- Run this SQL in your hosting provider's phpMyAdmin or MySQL interface

CREATE DATABASE IF NOT EXISTS if0_40818497_agritrust CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE if0_40818497_agritrust;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    wallet_address VARCHAR(42) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_wallet (wallet_address),
    INDEX idx_username (username)
) ENGINE=InnoDB;

-- Harvests table
CREATE TABLE harvests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    crop_type VARCHAR(100) NOT NULL,
    image_hash VARCHAR(64) NOT NULL, -- SHA256 hash of image
    ai_data JSON, -- AI verification results
    status ENUM('pending', 'verified', 'rejected', 'minted') DEFAULT 'pending',
    tx_hash VARCHAR(66), -- Blockchain transaction hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Insert demo user for testing
INSERT INTO users (username, wallet_address) VALUES 
('demo_farmer', '0x742d35Cc6634C0532925a3b8D0C9964De7C0C0C0');

-- Sample harvest record
INSERT INTO harvests (user_id, crop_type, image_hash, ai_data, status) VALUES 
(1, 'Maize', 'sample_hash_123', '{"crop": "Maize", "quantity": "50 bags", "value": "$1200", "confidence": 0.95}', 'verified');