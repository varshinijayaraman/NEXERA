<<<<<<< HEAD
-- NEXERA Database Schema
-- Run this script in phpMyAdmin or MySQL CLI to set up the database structure.

CREATE DATABASE IF NOT EXISTS nexera_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexera_db;

-- Users table stores authentication credentials and role metadata.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'student', 'parent') NOT NULL,
    status ENUM('active', 'inactive', 'force_reset') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME NULL
) ENGINE=InnoDB;

-- Students table captures core academic profile information.
CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    dob DATE NOT NULL,
    address TEXT NULL,
    phone VARCHAR(20) NULL,
    father_name VARCHAR(120) NULL,
    mother_name VARCHAR(120) NULL,
    parent_user_id INT UNSIGNED NULL,
    hosteller ENUM('hosteller', 'day_scholar') DEFAULT 'day_scholar',
    course VARCHAR(120) NULL,
    branch VARCHAR(120) NULL,
    year_of_study VARCHAR(50) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_students_parent FOREIGN KEY (parent_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Parents table stores guardian details.
CREATE TABLE IF NOT EXISTS parents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    parent_of VARCHAR(150) NULL,
    dob DATE NULL,
    father_name VARCHAR(120) NULL,
    mother_name VARCHAR(120) NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    linked_student_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_parents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_parents_student FOREIGN KEY (linked_student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Staff table distinguishes teaching and non-teaching members.
CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    position VARCHAR(100) NULL,
    teaching TINYINT(1) DEFAULT 1,
    qualifications TEXT NULL,
    schedule_json JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Attendance records for students.
CREATE TABLE IF NOT EXISTS attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'on_duty', 'leave') NOT NULL DEFAULT 'present',
    remarks VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_attendance (student_id, date)
) ENGINE=InnoDB;

-- Internal assessment scores.
CREATE TABLE IF NOT EXISTS internals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    subject VARCHAR(120) NOT NULL,
    marks INT UNSIGNED NOT NULL,
    max_marks INT UNSIGNED NOT NULL,
    term VARCHAR(50) NOT NULL,
    feedback TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_internals_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_internal (student_id, subject, term)
) ENGINE=InnoDB;

-- Leave/on-duty requests.
CREATE TABLE IF NOT EXISTS leaves (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT UNSIGNED NULL,
    CONSTRAINT fk_leaves_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_leaves_staff FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Placement tracking.
CREATE TABLE IF NOT EXISTS placements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(120) NOT NULL,
    status VARCHAR(60) NOT NULL DEFAULT 'applied',
    date_applied DATE NOT NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_placements_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Document vault for study materials.
CREATE TABLE IF NOT EXISTS documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploader_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    visibility_role ENUM('student', 'parent', 'staff', 'all') DEFAULT 'all',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_uploader FOREIGN KEY (uploader_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tasks and assignments.
CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assigned_by INT UNSIGNED NOT NULL,
    assigned_to_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    due_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'assigned',
    grade VARCHAR(50) NULL,
    feedback TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_staff FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_student FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Fee tracking.
CREATE TABLE IF NOT EXISTS fees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    amount_due DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    due_date DATE NOT NULL,
    payment_method VARCHAR(80) NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fees_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Audit trail for security visibility.
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    timestamp DATETIME NOT NULL,
    ip VARCHAR(64) NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;


=======
-- NEXERA Database Schema
-- Run this script in phpMyAdmin or MySQL CLI to set up the database structure.

CREATE DATABASE IF NOT EXISTS nexera_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexera_db;

-- Users table stores authentication credentials and role metadata.
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'student', 'parent') NOT NULL,
    status ENUM('active', 'inactive', 'force_reset') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME NULL
) ENGINE=InnoDB;

-- Students table captures core academic profile information.
CREATE TABLE IF NOT EXISTS students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NULL,
    dob DATE NOT NULL,
    address TEXT NULL,
    phone VARCHAR(20) NULL,
    father_name VARCHAR(120) NULL,
    mother_name VARCHAR(120) NULL,
    parent_user_id INT UNSIGNED NULL,
    hosteller ENUM('hosteller', 'day_scholar') DEFAULT 'day_scholar',
    course VARCHAR(120) NULL,
    branch VARCHAR(120) NULL,
    year_of_study VARCHAR(50) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_students_parent FOREIGN KEY (parent_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Parents table stores guardian details.
CREATE TABLE IF NOT EXISTS parents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    parent_of VARCHAR(150) NULL,
    dob DATE NULL,
    father_name VARCHAR(120) NULL,
    mother_name VARCHAR(120) NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    linked_student_id INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_parents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_parents_student FOREIGN KEY (linked_student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Staff table distinguishes teaching and non-teaching members.
CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    position VARCHAR(100) NULL,
    teaching TINYINT(1) DEFAULT 1,
    qualifications TEXT NULL,
    schedule_json JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Attendance records for students.
CREATE TABLE IF NOT EXISTS attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'on_duty', 'leave') NOT NULL DEFAULT 'present',
    remarks VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_attendance (student_id, date)
) ENGINE=InnoDB;

-- Internal assessment scores.
CREATE TABLE IF NOT EXISTS internals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    subject VARCHAR(120) NOT NULL,
    marks INT UNSIGNED NOT NULL,
    max_marks INT UNSIGNED NOT NULL,
    term VARCHAR(50) NOT NULL,
    feedback TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_internals_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_internal (student_id, subject, term)
) ENGINE=InnoDB;

-- Leave/on-duty requests.
CREATE TABLE IF NOT EXISTS leaves (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT UNSIGNED NULL,
    CONSTRAINT fk_leaves_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_leaves_staff FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Placement tracking.
CREATE TABLE IF NOT EXISTS placements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    company VARCHAR(150) NOT NULL,
    position VARCHAR(120) NOT NULL,
    status VARCHAR(60) NOT NULL DEFAULT 'applied',
    date_applied DATE NOT NULL,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_placements_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Document vault for study materials.
CREATE TABLE IF NOT EXISTS documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uploader_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    visibility_role ENUM('student', 'parent', 'staff', 'all') DEFAULT 'all',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_uploader FOREIGN KEY (uploader_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tasks and assignments.
CREATE TABLE IF NOT EXISTS tasks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assigned_by INT UNSIGNED NOT NULL,
    assigned_to_user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    due_date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'assigned',
    grade VARCHAR(50) NULL,
    feedback TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_staff FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_student FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Fee tracking.
CREATE TABLE IF NOT EXISTS fees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    amount_due DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    due_date DATE NOT NULL,
    payment_method VARCHAR(80) NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fees_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Audit trail for security visibility.
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    timestamp DATETIME NOT NULL,
    ip VARCHAR(64) NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;


>>>>>>> 703b86c6e5612ab3a8c616b821cd2ca2d7ee0f31
