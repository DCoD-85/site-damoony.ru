# Аудит Damoony2 — v3

**Дата:** 2026-10-18
**Предыдущие аудиты:** удалены (audit-10.10, audit-14.10, v1, v2, adminpanel)
**Заменяет:** всё, что было в `_audit/` до 18.10.2026

---

## 1. Оценка: 6.5 / 10

| Категория | Оценка |
|---|---|
| Архитектура | 8.0 |
| Безопасность | 4.5 |
| Инфраструктура (PHPStan/PHPUnit) | 7.5 |
| Тестируемость | 6.5 |
| Legacy-долг | 4.5 |

**Траектория 14.10 → 18.10: +0.8.** Миграция 122 файлов, 6 P0 закрыто, `431/431`, PHPStan `[OK]`, README переписан с ложных 8.0 на 6.5.

---

## 2. Целевая структура

engine/ ← ядро (125 файлов, включая engine/visuo/sources/)
logic/ ← домен (alpha.0.0)
corescripts/ ← CLI (13 команд + _lib)
www/ ← публичное (index.php, .htaccess, admin/, tests/)
config/ ← версионируемые конфиги
secure/ ← секреты (git-контроль частичный)
language/ ← .inc (не публично, читается движком)
_dev/ ← bin/, phpunit/, bootstrap.php, phpstan.neon, phpunit.xml
_audit/ ← v3 (эта папка)
_archive/ ← архив (в .gitignore)


**Правило локализации:** каждый блок открывается и описывается без заглядывания в другие (кроме явных ссылок вида «см. engine/db»).

| Блок | Файл аудита |
|---|---|
| engine | `01-engine.md` |
| config + secure + language | `02-config.md` |
| logic | `03-logic.md` |
| corescripts + _dev | `04-cli.md` |
| www | `05-www.md` |

---

## 3. Открытые P0 (6)

| ID | Файл | Проблема |
|---|---|---|
| P0-1 | `engine/user.php:116-118` | `setcookie('password', $password)` — plaintext. `engine/main.php:73` вызывает `new user($this)` — **P0-1 активен** |
| P0-2 | `logic/.../algoritm.php:54-62` | CORS через `HTTP_REFERER` + `Allow-Credentials: true` |
| P0-3 | `corescripts/commands/DomainRemove.php:42` | `rmrf` до `$cfg->save()` |
| P0-4 | `corescripts/commands/AlliasRemove.php:55` | то же |
| P0-5 | `logic/.../menu.php:55` vs `menu_row.php:40` | `text()` сигнатуры несовместимы |
| P0-6 | `engine/visuo/sources/visuorelease.php:88` | `visuopage->apply($struct)` без `instanceof` |

---

## 4. Новые P0 (middleware.php, создан 17.10)

| ID | Проблема |
|---|---|
| M-1 | `rateLimit` — `file_get_contents` без `flock` → race. 10/min даёт 20/min |
| M-2 | `rateLimit` — `REMOTE_ADDR` без trusted-proxy |
| M-3 | `die('Too Many Requests')` — `StopMiddleware` не используется |
| M-4 | `securityHeaders` без CSP (CSP только в `admin/bootstrap.php:35`) |

---

## 5. План

**P0 (6+4)** — 2–3 дня: `user.php` (миграция или выпил), CORS, транзакции, `menu::text`, `visuorelease::instanceof`, `middleware::rateLimit` (flock + proxy + StopMiddleware + CSP).

**P1 (6)** — неделя: `class_exists` в `include_ext`, `algoritm_.php:$base[1]`, admin `.data.json` race, git-гигиена (`config/site.config`, `secure/db.secure` в git).

**P2 (6)** — месяц: единый логгер, кэш JSON, `Scan::blocksFor` без хардкода, `db_mysqli_query` free/close, HTTP-коды 401/404.

**P3** — бэклог: PSR-4, `strict_types`, PHPStan L6, интеграционные тесты logic/, вынос visuo/.

---

## 6. Ссылки

- [01-engine.md](01-engine.md)
- [02-config.md](02-config.md)
- [03-logic.md](03-logic.md)
- [04-cli.md](04-cli.md)
- [05-www.md](05-www.md)
- [99-status-matrix.md](99-status-matrix.md)