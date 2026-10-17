# Аудиты проекта Damoony2

## Текущий статус (18.10.2026)

- **Оценка проекта:** 6.5 / 10 (было 5.7 — 14.10.2026)
- **Тесты:** 431 / 431 (проверено 18.10, `_dev/phpunit.phar`)
- **PHPStan:** level 5, `[OK] No errors`, baseline = 439 строк
- **Сайт:** main 307, admin 302, tests 200
- **Структура:** `engine/` (125 файлов), `logic/`, `corescripts/`, `www/`, `config/`, `secure/`
- **Миграция:** `sources/ → engine/` завершена (merge `6d41455`)

## Открытые P0 (по состоянию на 18.10)

- **`engine/user.php`** — plaintext пароли в cookie (`setcookie('password', ...)`)
- **`logic/.../algoritm/algoritm.php:54-62`** — CORS через `HTTP_REFERER` + `Allow-Credentials: true`
- **`engine/middleware.php::rateLimit`** — race: `file_get_contents` без `flock`
- **`engine/middleware.php::rateLimit`** — `REMOTE_ADDR` без trusted-proxy
- **`corescripts/commands/DomainRemove.php:42`** — `rmrf` до `$cfg->save()` (нет отката)
- **`corescripts/commands/AlliasRemove.php:55`** — то же
- **`logic/.../alliasglobal/menu.php:55` vs `menu_row.php:40`** — `text()` сигнатуры несовместимы
- **`engine/visuo/sources/visuorelease.php:88`** — `apply($struct)` без `instanceof`

## Закрытые P0 (с 14.10)

- Cookie `secure=true` на HTTP → `'secure' => $is_https` (`www/index.php:63`)
- `error()` пуст → реализован (`www/index.php:83`)
- `defined(TRUE)` → `defined('GDEF_SECURE_CONSTANT')` (`www/index.php:73`)
- Brute-force на login → `middleware::rateLimit('admin:login', 10, 60, 1800)`
- Login CSRF → `Csrf::field()` в `login.php:9` + проверка в `bootstrap.php:93`
- Config race → `flock(LOCK_EX)` в `corescripts/_lib/Config.php:138`
- `$GLOBALS['start']` / `$GLOBALS['start_hr']` → определены (`www/index.php:8-9`)
- `find($eq)` в `visuotag` → реализован (`visuotag.php:284-307`)

## Проблемы в git (проверить)

- **`secure/db.secure`** — 164 байта, что внутри? (в git)
- **`config/site.config`** — admin.hash? (в git)
- **`secure/admin_fails.json`** — runtime-файл (в git, мёртвый — не используется)
- **`config/site.config.lock`** — пустой файл (в git)

## Структура `_audit/`

_audit/
├── README.md ← этот файл (актуальный статус)
├── 00-index.md ← сводка, матрица «было → стало»
├── 01-engine.md ← ядро: engine/*
├── 02-config.md ← конфиг: config/, secure/, language/, data/
├── 03-logic.md ← логика: logic/alpha.0.0/
├── 04-cli.md ← CLI: corescripts/ + _dev/
├── 05-www.md ← WWW: www/*, www/.htaccess, www/admin/, www/tests/
└── 99-status-matrix.md ← проверяемая матрица P0


## История

- **17.09** — Initial commit (после аудита и cleanup)
- **14.10** — Первичный аудит 5 слоёв (5.7/10)
- **17.10** — middleware, security headers, audit log, IP whitelist admin
- **18.10** — `sources/ → engine/` миграция завершена (merge `6d41455`)
- **18.10** — Старые аудиты удалены. v3 (эта папка) — новый формат.

## Связанные коммиты

- `6d41455` — merge миграции
- `9e6a807` — перенос 122 файлов
- `e8a7160` — правки путей
- `a6dcd16` — fix `main.php:60` (self-reference)
- `99536e2` — fix PHPUnit (Stubs до autoload)
- `789fcb0` — baseline перегенерирован