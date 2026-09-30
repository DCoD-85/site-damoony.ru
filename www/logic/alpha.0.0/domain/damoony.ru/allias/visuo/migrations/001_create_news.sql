-- 001_create_news.sql
-- Создано: 2026-09-30
-- Назначение: таблица новостей проекта visuo.
-- Зависимости: users (FK author_id).

CREATE TABLE IF NOT EXISTS `news` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`         VARCHAR(128) NOT NULL,
    `title`        VARCHAR(255) NOT NULL,
    `excerpt`      VARCHAR(500) NOT NULL DEFAULT '',
    `content_md`   MEDIUMTEXT NULL,
    `content_html` MEDIUMTEXT NULL,
    `status`       ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `author_id`    INT UNSIGNED NULL,
    `lang`         VARCHAR(8) NOT NULL DEFAULT 'ru-ru',
    `published_at` DATETIME NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_slug_lang` (`slug`,`lang`),
    KEY `idx_published` (`status`,`published_at`),
    KEY `idx_lang_status_published` (`lang`,`status`,`published_at`),
    KEY `idx_author` (`author_id`),
    CONSTRAINT `fk_news_author`
        FOREIGN KEY (`author_id`) REFERENCES `users`(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;