-- 007_create_login_attempts.sql
-- Создано: 2026-09-30
-- Назначение: журнал попыток входа visuo — защита от brute-force.
-- Логика: 5 неудачных за 15 минут → блок IP на 15 минут.
-- Ротация: DELETE FROM login_attempts WHERE created_at < NOW() - INTERVAL 1 DAY;
--          (через cron или при логине раз в N запросов).

CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `login`      VARCHAR(64) NOT NULL DEFAULT '',
    `ip`         VARCHAR(45) NOT NULL DEFAULT '',
    `success`    TINYINT(1)  NOT NULL DEFAULT 0,
    `created_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_login_time` (`login`,`created_at`),
    KEY `idx_ip_time`    (`ip`,`created_at`),
    KEY `idx_created`    (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;