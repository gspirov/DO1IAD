CREATE DATABASE IF NOT EXISTS course;

USE course;

CREATE TABLE `user` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    grade INT NOT NULL DEFAULT 0 CHECK (grade >= 0 AND grade <= 100),
    CONSTRAINT uq_user_username UNIQUE (`username`)
) ENGINE = InnoDB;

CREATE TABLE course (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id VARCHAR(25) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    CONSTRAINT uq_course_course_id UNIQUE (course_id)
) ENGINE = InnoDB;

CREATE TABLE user_course (
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    PRIMARY KEY (`user_id`, `course_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
    FOREIGN KEY (`course_id`) REFERENCES `course`(`id`)
) ENGINE = InnoDB;

INSERT INTO `course` (course_id, `name`)
VALUES
('CC1IAD', 'Internet Applications and Database Design'),
('CS1CS', 'Computer Systems');