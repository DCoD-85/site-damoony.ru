-- 005_seed_files.sql
-- Создано: 2026-09-30
-- Назначение: начальная запись о шаблонном архиве visuo.
-- Файл template-1.0.0.zip должен лежать в www/media/downloads/.
-- Filesize здесь 0; после установки файла — обновить:
--   UPDATE files SET filesize = <bytes> WHERE slug = 'template';

INSERT INTO `files`
    (`slug`, `title`, `description`, `filename`, `filesize`, `mime_type`, `version`, `status`)
VALUES
    ('template',
     'Шаблон visuo 1.0.0',
     'Шаблонная версия проекта visuo. Архив с базовой структурой и конфигами.',
     'template-1.0.0.zip',
     0,
     'application/zip',
     '1.0.0',
     'active')
ON DUPLICATE KEY UPDATE
    `title`       = VALUES(`title`),
    `description` = VALUES(`description`),
    `filename`    = VALUES(`filename`),
    `mime_type`   = VALUES(`mime_type`),
    `version`     = VALUES(`version`),
    `status`      = VALUES(`status`);