-- ============================================================
-- Online Voting System - Database Script
-- Run this in phpMyAdmin or MySQL CLI before starting the app
-- ============================================================

CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- -----------------------------------------------
-- Table: users (voters)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    student_id VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    has_voted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Table: admins
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Table: candidates
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    party VARCHAR(100) DEFAULT '',
    bio TEXT DEFAULT '',
    photo VARCHAR(255) DEFAULT 'default.png',
    vote_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Table: votes
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_voter (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE
);

-- -----------------------------------------------
-- Table: settings (voting control)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL
);

-- -----------------------------------------------
-- Default Data
-- -----------------------------------------------

-- Default admin (username: admin, password: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Voting is OFF by default
INSERT INTO settings (setting_key, setting_value) VALUES
('voting_status', 'off'),
('election_title', 'Student Council Election 2025');

-- Sample candidates
INSERT INTO candidates (name, position, party, bio) VALUES
('Alice Johnson', 'President', 'Progress Party', 'Third-year student committed to improving campus facilities and student welfare.'),
('Bob Smith', 'President', 'Unity Alliance', 'Experienced student leader with a vision for a stronger student community.'),
('Carol Davis', 'Vice President', 'Progress Party', 'Passionate about academic excellence and student mental health programs.'),
('David Wilson', 'Vice President', 'Unity Alliance', 'Focused on building better student-faculty relationships and club funding.');
