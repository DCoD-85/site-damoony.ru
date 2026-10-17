# Аудиты проекта

## Текущий статус (17.10.2026)

- **Оценка проекта:** 8.0 / 10
- **Тесты:** 431 / 431 (PHPUnit + браузерный раннер)
- **PHPStan:** level 5, baseline = 1134 строки
- **P0-проблемы:** закрыты кроме `sources/user.php` (plaintext пароли — требует миграции БД)

## Что закрыто

- CORS, CSRF, brute-force, race conditions
- XSS в visuo (`esc()`)
- `find($eq)` в `visuotag`
- Security headers + audit log + rate-limit (login 10/min)
- Структура: engine/, logic/, _dev/

## История

- 14.10.2026 — первичный аудит 5 слоёв (5.7/10)
- 15.10.2026 — corescripts, админка, security
- 16.10.2026 — visuo, тесты 431/431
- 17.10.2026 — _dev/, security headers, audit log, middleware, структура