# Changelog

Все значимые изменения проекта фиксируются здесь.  
Формат: [Keep a Changelog](https://keepachangelog.com/ru/1.0.0/).

## [0.1.0] — 2026-09-30

Первый публичный снимок. Аллиас `visuo` работает, бэкапы настроены, git в порядке.

### Added

**Ядро:**
- `www/main.php` — ядро (main, main_settings, main_path)
- `www/logic/alpha.0.0/algoritm_.php` — роутер с regex-маршрутами
- `sources/db.php` + `sources/db/mysqli/` — слой абстракции над СУБД
- `sources/language.php` — загрузка языковых `.inc`-файлов

**Аллиас `visuo`:**
- Страницы: index, login, registration, logout, profile, docs, news, downloads
- Dataprovider: user, doc, news, files, stats, audit
- Миграции 001–007
- `visuo\user` — собственный пользователь (extends `\user`)
- `helpers.php` — CSRF, time_local, slugify, h()

**Мультиязычность:**
- `ru-ru.inc`, `en-en.inc` — дополнены ключами visuo
- `ch-ch.inc` — упрощённый китайский (новый)

**Alliasglobal:**
- `alliasblock.php` — база блоков
- `menu.php` + `menu_row.php` — меню
- `top`, `left`, `right`, `center`, `bottom`, `policy`
- `script/head.php`, `script/headers.php`

**Инфраструктура боевого:**
- HDD `/dev/sdb` → ext4, `/mnt/data` (916 ГБ)
- Timeshift — система, ежедневно
- Бэкап БД/конфигов/файлов — cron
- logrotate для логов бэкапа
- trash-cli для защиты от `rm`

**Git:**
- `.gitattributes` — нормализация line endings (LF)
- Одна основная ветка `main`

### Changed

- Удалены: `sources_old/`, `_archive/`, `www/media/plyr/`, `www/media/social/`, `www/media/fonts/` — 238 неиспользуемых файлов

### Fixed

- Восстановлена структура `/var/` после случайного перемещения в Trash
- `/var/run` → симлинк на `/run`
- Line endings приведены к LF

### Known issues

- Нет staging-окружения
- Нет автоматических тестов
- Нет внешнего мониторинга
- Права ролей захардкожены в `user::PERMISSIONS`

## [Unreleased]

### Planned

- Staging-окружение
- Smoke-тесты
- Внешний healthcheck
- Документация visuoel и visuotag
- Clonezilla-образ SSD