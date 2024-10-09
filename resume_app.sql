-- Adminer 4.8.1 MySQL 5.7.44 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_group VARCHAR(100),
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);

CREATE TABLE job_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(255) NOT NULL,
    qualification TEXT,
    experience_years INT,
    skills TEXT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE resume_processing_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role INT,
    status VARCHAR(50),
    created_by INT,
    last_updated_by INT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE resume_insights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_id INT,
    original_file_path TEXT,
    original_file_name VARCHAR(255),
    experience INT,
    candidate_name VARCHAR(255),
    skills TEXT,
    matched_skills TEXT,
    score DECIMAL(5,2),
    status VARCHAR(50),
    insights TEXT,
    token_usage INT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE resume_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resume_id INT,
    resume_text TEXT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO `users` (`user_group`, `username`, `password`, `created_date`, `updated_date`)
VALUES ('admin', 'swapnil', md5('password'), now(), now());

INSERT INTO `job_requirements` (`id`, `role_name`, `qualification`, `experience_years`, `skills`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1,	'PHP Developer',	'BE',	2,	'PHP,JAVASCRIPT',	'FULL STACK WEB DEVELOPER',	1,	'2023-05-20 09:44:09',	'2023-05-23 12:52:55'),
(2,	'Seniour PHP Developer',	'BE',	4,	'PHP , MYSQL , JAVASCRIPT ',	'We are seeking an experienced and talented Sr. PHP Full Stack Developer to join our dynamic development team. As a Sr. PHP Full Stack Developer, you will play a key role in designing, developing, and maintaining high-quality web applications and solutions. You will be responsible for both front-end and back-end development, ensuring optimal performance, security, and user experience.',	1,	'2023-05-29 10:04:59',	'2023-05-29 10:04:59');





-- 2024-06-26 16:40:06
