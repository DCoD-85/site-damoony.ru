-- 002_create_files.sql
-- Создано: 2026-09-30
-- Назначение: таблица файлов для скачивания.
-- Файлы физически лежат в www/media/downloads/.

CREATE TABLE IF NOT EXISTS `files` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(128) NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `filename`    VARCHAR(255) NOT NULL,
    `filesize`    INT UNSIGNED NOT NULL DEFAULT 0,
    `mime_type`   VARCHAR(128) NOT NULL DEFAULT 'application/octet-stream',
    `version`     VARCHAR(32) NOT NULL DEFAULT '',
    `downloads`   INT UNSIGNED NOT NULL DEFAULT 0,
    `status`      ENUM('active','hidden') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_slug` (`slug`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;