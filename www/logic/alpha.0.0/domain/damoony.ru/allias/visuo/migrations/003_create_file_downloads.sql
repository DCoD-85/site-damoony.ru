-- 003_create_file_downloads.sql
-- Создано: 2026-09-30
-- Назначение: журнал скачиваний (статистика + аудит).
-- Зависимости: files (FK file_id), users (FK user_id, nullable).

CREATE TABLE IF NOT EXISTS `file_downloads` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `file_id`    INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NULL,
    `ip`         VARCHAR(45) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_file` (`file_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_downloads_file`
        FOREIGN KEY (`file_id`) REFERENCES `files`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT `fk_downloads_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;