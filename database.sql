-- Work Reminder and Chat Bot System Database Schema
-- B.Sc Computer Science Final Year Project

-- Create database
CREATE DATABASE IF NOT EXISTS work_reminder_db;
USE work_reminder_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Tasks table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    task_date DATE NOT NULL,
    task_time TIME NOT NULL,
    task_type ENUM('daily', 'monthly') NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    reminder_sent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, task_date),
    INDEX idx_task_datetime (task_date, task_time)
);

-- Chatbot logs table
CREATE TABLE chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_query TEXT NOT NULL,
    bot_response TEXT NOT NULL,
    query_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_query_time (user_id, query_time)
);

-- Admin logs table
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_user_id INT NULL,
    details TEXT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_admin_action_time (admin_id, action_time)
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@workreminder.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin');

-- Insert sample regular users
INSERT INTO users (username, email, password, full_name, role) VALUES 
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'user');

-- Insert sample tasks for John Doe
INSERT INTO tasks (user_id, title, description, task_date, task_time, task_type, priority) VALUES 
(2, 'Team Meeting', 'Weekly team sync meeting', CURDATE(), '10:00:00', 'daily', 'high'),
(2, 'Project Deadline', 'Submit final project report', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '17:00:00', 'monthly', 'high'),
(2, 'Code Review', 'Review pull requests', CURDATE(), '14:30:00', 'daily', 'medium'),
(2, 'Client Call', 'Discuss project requirements', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '11:00:00', 'daily', 'high');

-- Insert sample tasks for Jane Smith
INSERT INTO tasks (user_id, title, description, task_date, task_time, task_type, priority) VALUES 
(3, 'Design Review', 'Review UI mockups', CURDATE(), '15:00:00', 'daily', 'medium'),
(3, 'Documentation', 'Update API documentation', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '16:00:00', 'monthly', 'low'),
(3, 'Training Session', 'Attend technical training', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'daily', 'medium');

-- Insert sample chatbot logs
INSERT INTO chatbot_logs (user_id, user_query, bot_response) VALUES 
(2, 'What tasks do I have today?', 'You have 2 tasks today: Team Meeting at 10:00 AM and Code Review at 2:30 PM.'),
(2, 'Show my monthly schedule', 'This month you have 2 monthly tasks including Project Deadline on 3 days from now.'),
(3, 'Remind me about my meeting tomorrow', 'I will remind you about your Client Call tomorrow at 11:00 AM.');
