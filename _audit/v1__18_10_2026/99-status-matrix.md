# 99 — Status Matrix (P0)

**Дата:** 2026-10-18
**Легенда:** ✅ закрыт / 🔴 жив / ⚠️ частично / 🆕 новый

| ID | Находка (14.10) | Статус | Доказательство / Комментарий |
|---|---|---|---|
| **P0-1** | `user.php` — plaintext пароли | 🔴 **ЖИВ** | `setcookie('password', ...)` в `engine/user.php:117`. `main.php:73` вызывает `new user($this)` |
| **P0-2** | CORS через `HTTP_REFERER` | 🔴 **ЖИВ** | `logic/.../algoritm.php:54-62` — без изменений |
| **P0-3** | `DomainRemove` без отката | 🔴 **ЖИВ** | `rmrf` до `save` (`DomainRemove.php:42`) |
| **P0-4** | `AlliasRemove` без отката | 🔴 **ЖИВ** | `rmrf` до `save` (`AlliasRemove.php:55`) |
| **P0-5** | `menu::text()` сигнатуры | 🔴 **ЖИВ** | 3 аргумента в `menu.php`, 1 в `menu_row.php` |
| **P0-6** | `visuorelease::apply` без instanceof | 🔴 **ЖИВ** | `visuorelease.php:88` |
| **M-1** | `rateLimit` race | 🆕 **НОВЫЙ** | `file_get_contents` без `flock` |
| **M-2** | `REMOTE_ADDR` без proxy | 🆕 **НОВЫЙ** | `middleware.php:41` |
| **M-3** | `die()` вместо `StopMiddleware` | 🆕 **НОВЫЙ** | `StopMiddleware` объявлен, но не брошен |
| **M-4** | CSP только в admin | 🆕 **НОВЫЙ** | `grep` не находит CSP в `engine/` |
| **G-1** | `config/site.config` в git | 🔴 **ЖИВ** | `git ls-files` подтверждает |
| **G-2** | `secure/db.secure` в git | 🔴 **ЖИВ** | `git ls-files` подтверждает |
| **G-3** | `admin_fails.json` в git | 🔴 **ЖИВ** | runtime-файл в git |
| **H-1** | `max_execution_time 5000` | ✅ **ЗАКРЫТ** | `9ab0901` (стало 300) |
| **H-2** | `upload_max_filesize 2000M` | ✅ **ЗАКРЫТ** | `9ab0901` (стало 64M) |
| **H-3** | `max_input_vars 100001` | ✅ **ЗАКРЫТ** | `9ab0901` (стало 5000) |
| **H-4** | `php_flag magic_quotes` | ✅ **ЗАКРЫТ** | `9ab0901` (удалены) |
| **H-5** | `RewriteCond` всегда true | 🔴 **ЖИВ** | `.htaccess` |
| **H-6** | `tests` redirect на любой путь | 🔴 **ЖИВ** | `.htaccess` |
| **A-1** | brute-force на login | ⚠️ **ЧАСТИЧНО** | rate-limit есть, но M-1 (race) |
| **A-2** | race в `.data.json` | 🔴 **ЖИВ** | admin `page:edit`/`page:toggle` |
| **A-3** | race в `site.config` | ✅ **ЗАКРЫТ** | `Config.php:138` (`flock(LOCK_EX)`) |
| **A-4** | нет отката при удалении | 🔴 **ЖИВ** | = P0-3, P0-4 |
| **A-5** | login CSRF | ✅ **ЗАКРЫТ** | `login.php:9` + `bootstrap.php:93` |
| **A-6** | хардкод `alpha.0.0` | ⚠️ **ЧАСТИЧНО** | `Scan::blocksFor` всё ещё хардкодит |
| **B-1** | `defined(TRUE)` | ✅ **ЗАКРЫТ** | `www/index.php:73` |
| **B-3/4** | `$GLOBALS['start*']` | ✅ **ЗАКРЫТ** | `www/index.php:8-9` |
| **S-2** | `secure=true` на HTTP | ✅ **ЗАКРЫТ** | `www/index.php:63` |
| **W-6** | `error()` пуст | ✅ **ЗАКРЫТ** | `www/index.php:83` |
| **visuo B-1** | `find()` без `$eq` | ✅ **ЗАКРЫТ** | `visuotag.php:284-307` |
| **E-1** | `db::update` / `delete` `%s` | 🔴 **ЖИВ** | int → coerce |
| **E-4** | `ahref` без url-encode | 🔴 **ЖИВ** | `library_link.php` |

---

## Итог

- **Закрыто P0 (из 14.10):** 6 из 12
- **Живых P0 (из 14.10):** 6
- **Новых P0 (middleware):** 4
- **Git-риски:** 3 (G-1…G-3)
- **`.htaccess`:** 3 из 7 закрыто (H-1…H-4), H-5/H-6 живы.

**Первоочередные задачи:**
1. P0-1 (plaintext пароли) — самый критичный.
2. M-1 (race в rate-limit) — делает brute-force защиту фиктивной.
3. G-1/G-2 (проверить секреты в git).
4. P0-3/P0-4 (транзакции).