DROP DATABASE IF EXISTS portfolio;
CREATE DATABASE portfolio
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

DROP DATABASE IF EXISTS portfolio_test;
CREATE DATABASE portfolio_test
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

USE portfolio;

DROP USER IF EXISTS 'web'@'localhost';
DROP USER IF EXISTS 'admin'@'localhost';

CREATE USER 'web'@'localhost' IDENTIFIED BY '2Yl6OKFNR4e8fLja';
CREATE USER 'admin'@'localhost' IDENTIFIED BY 'GPKV23Wbwb81oqzi';

GRANT SELECT, INSERT, UPDATE, DELETE
    ON portfolio.*
    TO 'web'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE,
    CREATE, ALTER, DROP, REFERENCES
    ON portfolio.*
    TO 'admin'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE
    ON portfolio_test.*
    TO 'web'@'localhost';

GRANT SELECT, INSERT, UPDATE, DELETE,
    CREATE, ALTER, DROP, REFERENCES
    ON portfolio_test.*
    TO 'admin'@'localhost';

FLUSH PRIVILEGES;

-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: portfolio
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache`
(
    `key`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `value`      mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci   NOT NULL,
    `expiration` int                                                           NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_expiration_index` (`expiration`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks`
(
    `key`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `owner`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `expiration` int                                                           NOT NULL,
    PRIMARY KEY (`key`),
    KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs`
(
    `id`         bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `uuid`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `queue`      text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `payload`    longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `exception`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `failed_at`  timestamp                                                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches`
(
    `id`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `name`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `total_jobs`     int                                                           NOT NULL,
    `pending_jobs`   int                                                           NOT NULL,
    `failed_jobs`    int                                                           NOT NULL,
    `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `options`        mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `cancelled_at`   int DEFAULT NULL,
    `created_at`     int                                                           NOT NULL,
    `finished_at`    int DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs`
(
    `id`           bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `queue`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `payload`      longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `attempts`     tinyint unsigned                                              NOT NULL,
    `reserved_at`  int unsigned DEFAULT NULL,
    `available_at` int unsigned                                                  NOT NULL,
    `created_at`   int unsigned                                                  NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations`
(
    `id`        int unsigned                                                  NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `batch`     int                                                           NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens`
(
    `email`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `token`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_comments`
--

DROP TABLE IF EXISTS `project_comments`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_comments`
(
    `id`         bigint unsigned                                       NOT NULL AUTO_INCREMENT,
    `project_id` bigint unsigned                                       NOT NULL,
    `user_id`    bigint unsigned                                       NOT NULL,
    `comment`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                             NULL DEFAULT NULL,
    `updated_at` timestamp                                             NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `project_comments_project_id_foreign` (`project_id`),
    KEY `project_comments_user_id_foreign` (`user_id`),
    CONSTRAINT `project_comments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_images`
--

DROP TABLE IF EXISTS `project_images`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_images`
(
    `id`         bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `project_id` bigint unsigned                                               NOT NULL,
    `path`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` timestamp                                                     NULL DEFAULT NULL,
    `updated_at` timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `project_images_project_id_foreign` (`project_id`),
    CONSTRAINT `project_images_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_ratings`
--

DROP TABLE IF EXISTS `project_ratings`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_ratings`
(
    `id`         bigint unsigned  NOT NULL AUTO_INCREMENT,
    `project_id` bigint unsigned  NOT NULL,
    `user_id`    bigint unsigned  NOT NULL,
    `rating`     tinyint unsigned NOT NULL,
    `created_at` timestamp        NULL DEFAULT NULL,
    `updated_at` timestamp        NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `project_ratings_project_id_user_id_unique` (`project_id`, `user_id`),
    KEY `project_ratings_user_id_foreign` (`user_id`),
    CONSTRAINT `project_ratings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `project_ratings_check_rating` CHECK (((`rating` >= 1) and (`rating` <= 5)))
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects`
(
    `id`                bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `title`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `start_date`        date                                                          NOT NULL,
    `end_date`          date                                                          NOT NULL,
    `short_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci         NOT NULL,
    `phase`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id`           bigint unsigned                                               NOT NULL,
    `created_at`        timestamp                                                     NULL DEFAULT NULL,
    `updated_at`        timestamp                                                     NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `projects_user_id_foreign` (`user_id`),
    CONSTRAINT `projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `projects_check_dates` CHECK ((`end_date` > `start_date`))
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions`
(
    `id`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_id`       bigint unsigned                                              DEFAULT NULL,
    `ip_address`    varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `user_agent`    text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    `payload`       longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci     NOT NULL,
    `last_activity` int                                                           NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_project_favourites`
--

DROP TABLE IF EXISTS `user_project_favourites`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_project_favourites`
(
    `id`         bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id`    bigint unsigned NOT NULL,
    `project_id` bigint unsigned NOT NULL,
    `created_at` timestamp       NULL DEFAULT NULL,
    `updated_at` timestamp       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_project_favourites_user_id_foreign` (`user_id`),
    KEY `user_project_favourites_project_id_foreign` (`project_id`),
    CONSTRAINT `user_project_favourites_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_project_favourites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users`
(
    `id`                bigint unsigned                                               NOT NULL AUTO_INCREMENT,
    `username`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `email`             varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at` timestamp                                                     NULL DEFAULT NULL,
    `password`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `remember_token`    varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    `created_at`        timestamp                                                     NULL DEFAULT NULL,
    `updated_at`        timestamp                                                     NULL DEFAULT NULL,
    `avatar`            varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci      DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_username_unique` (`username`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2026-04-20 16:47:21
