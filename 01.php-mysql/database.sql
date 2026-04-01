-- ============================================================
-- Student Management System — Full Database Setup
-- MySQL 8 / MariaDB 10+
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_system;

USE student_system;


-- ── users ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(150) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','student') NOT NULL DEFAULT 'student',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── students ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
    id              INT           AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL UNIQUE,
    student_code    VARCHAR(20)   NOT NULL UNIQUE,   -- e.g. STU-2026-0001
    full_name       VARCHAR(100)  NOT NULL,
    email           VARCHAR(150)  NOT NULL,
    phone           VARCHAR(30)   DEFAULT NULL,
    date_of_birth   DATE          DEFAULT NULL,
    gender          ENUM('Male','Female','Other') DEFAULT NULL,
    department      VARCHAR(100)  DEFAULT NULL,
    year_of_study   TINYINT       DEFAULT NULL,
    address         TEXT          DEFAULT NULL,
    profile_picture VARCHAR(255)  NOT NULL DEFAULT 'default.png',
    gpa             DECIMAL(3,2)  DEFAULT NULL,
    enrollment_date DATE          DEFAULT NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── seed: one admin account (password = Admin@1234) ──────────
-- ── To Run create for admin hash password ──────────
-- ── php -r "echo password_hash('Admin@1234', PASSWORD_BCRYPT);"  ──────────

INSERT IGNORE INTO users (username, email, password, role)
VALUES (
    'admin',
    'admin@university.edu',
    '$2y$10$7NgdJiZsRh7bV1894tx1Re5YnyFwuMF5iSEJWtpO0jx/bv55qi9ZS',
    'admin'
);

