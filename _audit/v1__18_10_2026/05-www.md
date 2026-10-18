# 05 — www/ (bootstrap + admin + .htaccess)

**Объект:** `www/` (index.php, .htaccess, admin/, tests/)
**Дата:** 2026-10-18
**Оценка:** 6.3 / 10 (архитектура 8, безопасность 5)

---

## 1. Резюме

Админка изолирована, использует `admin_sid`, CSRF и brute-force защиту. Основные проблемы: `.htaccess` rewrite-логика и хардкод путей в админке.

---

## 2. .htaccess (H-5, H-6, H-7)

**H-5:** цепочка `RewriteCond` с `!-f [OR] !-d [OR] ... -f` — логически всегда true.

**H-6:** `RewriteCond %{REQUEST_FILENAME} ^(.*)tests(.*)$` — матчит любой путь с `tests`, включая `my-tests.js`.

**H-7:** `Options +FollowSymlinks` + `Options -Indexes` — можно одной строкой.

**Фикс:**
- H-5: заменить на `RewriteCond %{REQUEST_FILENAME} !-f` / `!-d`.
- H-6: сделать точное совпадение `^/tests/`.
- H-7: `Options +FollowSymlinks -Indexes`.

---

## 3. Админка (admin/)

**Закрыто:** brute-force (rate-limit), login CSRF, IP whitelist.

**Осталось (P1/P2):**
- `Auth::check()` не сверяет `admin_login` с текущим конфигом.
- `_layout.php` читает `$_SESSION` напрямую.
- 16× `new Config()` в `bootstrap.php` (нужен lazy-singleton).
- Хардкод `alpha.0.0` в `filler:new`, `Scan::blocksFor`.

---

## 4. www/index.php

**Закрыто:** `secure=true` на HTTP (теперь `$is_https`), `error()` реализован, `defined(TRUE)` исправлен.

**Осталось:**
- `$GLOBALS['start']` определён (визуально работает).
- `middleware` вызывается — но M-1…M-4 (см. 01-engine.md).

---

## 5. www/tests/

`tests/index.php` и `tests/list.php` — dev-раннер, закрыт `.htaccess` (H-6). После миграции пути исправлены на `engine/`.

---

## 6. Рекомендации

**P1:**
1. `.htaccess` — починить H-5, H-6, H-7.
2. `Scan::blocksFor` — убрать хардкод `alpha.0.0`.

**P2:**
3. Lazy-singleton для `Config` в админке.
4. `_layout.php` — передавать данные через `View::render()`.