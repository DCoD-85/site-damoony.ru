-- 006_seed_news.sql
-- Создано: 2026-09-30
-- Назначение: 3 тестовые новости на ru-ru.
-- Автор — пользователь с логином admin.

SET @admin_id = (SELECT `id` FROM `users` WHERE `login` = 'admin' LIMIT 1);

INSERT INTO `news`
    (`slug`, `title`, `excerpt`, `content_html`, `content_md`, `status`, `author_id`, `lang`, `published_at`)
VALUES
    ('visuo-start',
     'Запуск проекта visuo',
     'Первая публичная версия UI-движка Damoony.',
     '<p>Мы запустили проект <strong>visuo</strong> — самостоятельный UI-движок на базе Damoony.</p><p>Что уже готово: документация, новости, загрузки, мультиязычность.</p>',
     NULL,
     'published',
     @admin_id,
     'ru-ru',
     NOW()),

    ('visuo-docs',
     'Открыт раздел документации',
     'Дерево разделов, статьи, версии.',
     '<p>Раздел <strong>документации</strong> открыт. Внутри — дерево разделов, статьи и история версий.</p>',
     NULL,
     'published',
     @admin_id,
     'ru-ru',
     NOW()),

    ('visuo-langs',
     'Поддержка трёх языков',
     'ru-ru, en-en, ch-ch — переключение работает.',
     '<p>visuo поддерживает три языка: <em>русский</em>, <em>английский</em>, <em>упрощённый китайский</em>.</p>',
     NULL,
     'published',
     @admin_id,
     'ru-ru',
     NOW())
ON DUPLICATE KEY UPDATE
    `title`        = VALUES(`title`),
    `excerpt`      = VALUES(`excerpt`),
    `content_html` = VALUES(`content_html`),
    `status`       = VALUES(`status`),
    `author_id`    = VALUES(`author_id`),
    `published_at` = VALUES(`published_at`);