-- 004_alter_users_timezone_lang.sql
-- Создано: 2026-09-30
-- Назначение: добавить в users часовой пояс и язык.
--
-- ВАЖНО: MySQL 8 не поддерживает ADD COLUMN IF NOT EXISTS.
-- Перед применением проверить, что колонок ещё нет:
--   SHOW COLUMNS FROM `users` LIKE 'timezone';
--   SHOW COLUMNS FROM `users` LIKE 'lang';
-- Если колонка уже есть — пропустить соответствующий ALTER.

ALTER TABLE `users`
    ADD COLUMN `timezone` VARCHAR(64) NOT NULL DEFAULT 'Europe/Moscow'
        COMMENT 'IANA timezone, e.g. Europe/Moscow'
        AFTER `last_login_at`;

ALTER TABLE `users`
    ADD COLUMN `lang` VARCHAR(8) NOT NULL DEFAULT 'ru-ru'
        COMMENT 'ru-ru | en-en | ch-ch'
        AFTER `timezone`;

ALTER TABLE `users`
    ADD KEY `idx_lang` (`lang`);