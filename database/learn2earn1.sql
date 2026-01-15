CREATE DATABASE IF NOT EXISTS learn2earn1;
USE learn2earn1;

-- USERS TABLE (COMMON FOR ALL ROLES)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('learner','instructor','client') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- LEARNER TABLE
CREATE TABLE learners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- INSTRUCTOR TABLE
CREATE TABLE instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- CLIENT TABLE
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- COURSES
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    title VARCHAR(150),
    description TEXT,
    difficulty ENUM('Beginner','Intermediate','Advanced'),
    FOREIGN KEY (instructor_id) REFERENCES instructors(id)
);

-- ENROLLMENTS
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    course_id INT NOT NULL,
    progress INT DEFAULT 0,
    FOREIGN KEY (learner_id) REFERENCES learners(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- JOBS
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    title VARCHAR(150),
    description TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- JOB APPLICATIONS
CREATE TABLE job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    learner_id INT NOT NULL,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    FOREIGN KEY (job_id) REFERENCES jobs(id),
    FOREIGN KEY (learner_id) REFERENCES learners(id)
);
