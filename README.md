# damoony.ru

Движок для мультиязычных сайтов на PHP 8.4. Аллиасная архитектура, разделение на слои, UI-движок visuo.

## Что это

Не CMS, а фреймворк — инструмент для сборки сайтов. Аллиасы (`www`, `login`, `visuo`) — независимые модули поверх общего ядра. Каждый аллиас работает со своей БД, своими сессиями, своими URL.

## Структура проекта

    damoony.ru/
    ├── www/                       веб-корень (DocumentRoot)
    │   ├── index.php              точка входа
    │   ├── main.php               ядро (main, main_settings)
    │   ├── logic/alpha.0.0/       движок роутинга
    │   │   ├── algoritm_.php      база роутера
    │   │   └── domain/damoony.ru/
    │   │       ├── allias/        аллиасы (www, login, visuo)
    │   │       ├── alliasglobal/  общие блоки (top, left, center, right, bottom)
    │   │       └── alliasscheme/  схемы страниц
    │   ├── language/              тексты (ru-ru, en-en, ch-ch)
    │   └── media/                 статика
    ├── sources/                   движок БД, language, visuo UI
    │   ├── db.php                 слой БД (mysqli-драйвер)
    │   ├── language.php           загрузка .inc-файлов
    │   └── visuo/                 visuotag, visuoel
    ├── secure/                    конфиги БД, ключи
    ├── config/                    site.config, render.config, lang.config
    ├── docs/                      документация
    └── CHANGELOG.md

## Требования

- PHP 8.4 (extensions: `mysqli`, `mbstring`, `json`)
- MariaDB 10.6+ или MySQL 8+
- Apache 2.4

## Быстрый старт (dev)

1. Клонировать репозиторий:

       git clone git@github.com:DCoD-85/site-damoony.ru.git
       cd site-damoony.ru

2. Настроить `secure/db.config` — хост, пользователь, пароль, БД.

3. Настроить `config/site.config` — домены и аллиасы.

4. Создать БД и применить миграции:

       mysql -u root -e "CREATE DATABASE visuo_damoony CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
       mysql -u root visuo_damoony < www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/migrations/apply_all.sql

5. Открыть в браузере: `http://visuo.damoony2.ru/`

## Рабочий процесс

**Локальный (dev) → Git → Боевой (prod).**

- Все правки — только локально.
- Коммит → push в GitHub.
- На боевом: `git pull`.
- Никогда не править файлы на боевом вручную.

## Документация

- [docs/architecture.md](docs/architecture.md) — слои, модули, роутинг
- [docs/deploy.md](docs/deploy.md) — деплой и откат
- [docs/backup.md](docs/backup.md) — бэкапы и восстановление
- [docs/development.md](docs/development.md) — правила разработки

## Лицензия

Private. Все права защищены.