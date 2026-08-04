CREATE DATABASE IF NOT EXISTS watch_sk_fund;
USE watch_sk_fund;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('sk_admin', 'lydo', 'sk_fed', 'verification', 'accountant', 'mayor_office', 'public') NOT NULL,
    barangay_name VARCHAR(100) NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL UNIQUE,
    reset_token VARCHAR(255) NULL,
    reset_token_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    abyip_code VARCHAR(100) NULL,
    budget_category VARCHAR(100) NULL,
    budget DECIMAL(15, 2) NOT NULL,
    status ENUM('planned', 'ongoing', 'completed') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

CREATE TABLE project_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    stage ENUM('planning', 'authorization', 'implementation', 'monitoring') NOT NULL,
    description TEXT,
    date_achieved DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_stage (stage)
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    type ENUM('disbursement', 'liquidation', 'roa', 'procurement') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    reference_no VARCHAR(100) NOT NULL,
    status ENUM('pending', 'reviewed', 'recorded', 'returned') DEFAULT 'pending',
    document_path VARCHAR(255) NULL,
    reviewed_by INT NULL,
    recorded_by INT NULL,
    remarks TEXT,
    deficiency_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_project_id (project_id),
    INDEX idx_status (status),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_reviewed_by (reviewed_by),
    INDEX idx_recorded_by (recorded_by)
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    month TINYINT NOT NULL,
    year YEAR NOT NULL,
    status ENUM('pending', 'reviewed', 'returned') DEFAULT 'pending',
    session_minutes_path VARCHAR(255) NULL,
    attendance_records_path VARCHAR(255) NULL,
    post_activity_reports_path VARCHAR(255) NULL,
    financial_reports_path VARCHAR(255) NULL,
    remarks TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_year_month (year, month),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
);

CREATE TABLE certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    type ENUM('skcc', 'review', 'recording') NOT NULL,
    account_number VARCHAR(100) NULL,
    check_number VARCHAR(100) NULL,
    cert_date DATE NULL,
    payee VARCHAR(255) NULL,
    amount DECIMAL(15, 2) NULL,
    purpose TEXT NULL,
    issued_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_issued_by (issued_by),
    INDEX idx_type (type)
);

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(100) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_read_at (read_at),
    INDEX idx_created_at (created_at)
);

-- Insert default admin users
INSERT INTO users (username, password, role, full_name, barangay_name, email) VALUES
('sk_admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sk_admin', 'SK Chairperson Juan', 'Barangay Poblacion', 'sk_admin1@example.com'),
('lydo_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lydo', 'LYDO Officer Maria', NULL, 'lydo_admin@example.com'),
('sk_fed', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sk_fed', 'SK Fed President Jose', NULL, 'sk_fed@example.com'),
('accountant', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'accountant', 'Municipal Accountant', NULL, 'accountant@example.com'),
('mayor_office', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mayor_office', 'Office of the Mayor Rep', NULL, 'mayor_office@example.com');
-- Note: Default password is 'password' for all.
