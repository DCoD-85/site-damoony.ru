# Аудит v2 — 19.10.2026 (Финал сессии)

## Закрыто (закоммичено в main)

### Безопасность
- **P0** admin2 login bypass — удалён из `toolsusers` (password='')
- **C2** cookie без `domain`: PHPSESSID, language, lang, skin, visuo_sid
- **C4** Host-валидация по `site.config:domains_real`, `<allias>.<domain>`
- **C3** X-Forwarded-Proto только от `trusted_proxies`
- **H2** порт отрезается из host (cookie domain)
- **H4** `set_error_handler`/`set_exception_handler`/`shutdown` в `index.php`
- **M1** CSP: убран `'unsafe-inline'` из `script-src` (осталось в `style-src`)
- **M2** `middleware::rateLimit` — `flock` на read-modify-write
- legacy `engine/user.php::login()`/`logout()` — удалены (мёртвые, P0-риск)

### Архитектура / баги
- A1 `require_once` → `require` в `alogic_bridge` (fatal при 2-м вызове)
- A5 `Router::add('/')` → матчит `/` и `''`
- X1 `__DIR__` в `alliasglobal.php` (3 домена)
- X2 `dataprovider_base::$localname` из `config:domain_db`
- U4 `library_url::domain()` → всегда `[host, allias]` (2 элемента)
- B2 `library_link::href` — пустой аллиас больше не даёт `.host`
- doctype `<!DOCTYPE html>` вместо `<! doctype>` (standards mode)
- CSS-пути без точки после аллиаса (`www.` → `www`)
- legacy `algoritm_::start()` — добавлен пустой стаб (убрана мина в `main::logic()`)

### JS / visuo (M1)
- Новый `engine_visuo.js` — единый контроллер `data-action`
- `engine_confidence.js` оставлен, `engine_visuotablebox.js` удалён (мёртвый)
- `visuoel_tabs_page` → `data-action="tabs-show"` (без `onclick`)
- `policy.php` → `data-action="confidence-show"`/`"confidence-hide"`
- `/tests/` — delegate для `tabs-show` (standalone runner)

### Тесты / CI (Инфраструктура)
- `_dev/bootstrap.php`: `VISUO_SRC` → `visuo/sources` (был `engine/visuo/sources`)
- `RouterTest` + `testRootMatch` (A5) + фикс phpunit кэша в `.gitignore`
- **436/436 тестов** проходят (было 435)
- **`.github/workflows/ci.yml`** — работает! PHPUnit + PHPStan на push/PR
- `_dev/phpstan.neon`: добавлены `scanDirectories` для `visuo` и `engines`, `constants.php` в `scanFiles` (устранены 29 ошибок `class.notFound`)
- PHPStan локально и в CI выдаёт `[OK] No errors` (Level 5, baseline 439)

## Открыто (roadmap)

### 🟠 важное
- **SSO (Шаг 2)** — `www` → `visuo\user` (сейчас на www нет реальной авторизации, всегда гость)
- **PHPStan baseline** — снижать с 439 до 0 (постепенно поднимая Level 6)

### 🟡 мелочи
- `alliasscheme_login.php` — путь `media/js/jsengine/` не существует (404 на JS в login-схеме)
- `hert.online`/`idoam.ru` — `onclick` в `policy.php`, `engine_visuotablebox.js` 404 (при активации)
- `publicsuffix` для `.co.uk` в `domain_for_sso`
- `__Host-` префикс при HTTPS для куки
- `visuorelease.php` debug `onclick` ×3 — только `GL_DEBUG=true`, dev-only
- `visio` дубли: `www/media/damoony.ru/` vs `shared/` vs `corescripts/templates/media/`
- `db_pass` qwe12345 — сменить до наполнения прода
- `<doctype>` `< !DOCTYPE html />` — работает, но не валидно (перейти на `raw_html`)

## Файлы изменены (ключевые)

- `www/index.php` — Host, cookie, H4, C3
- `engine/middleware.php` — CSP, rateLimit flock
- `engine/cookie.php` — новый класс (cookie::set, cookie::delete, domain_for_sso)
- `engine/library/url.php`, `engine/library/link.php` — U4, B2
- `engine/user.php`, `engines/legacy/algoritm_.php` — минус мёртвый код, плюс стаб
- `engines/alogic/alogic_bridge.php`, `engines/alogic/Router.php` — A1, A5
- `app/.../allias/visuo/user.php`, `dataprovider_base.php` — cookie, X2
- `www/media/shared/scripts/engine/engine_visuo.js` — новый JS-контроллер
- `_dev/bootstrap.php`, `_dev/phpunit/RouterTest.php`, `_dev/phpstan.neon` — тесты, статик-анализ
- `.github/workflows/ci.yml` — CI pipeline