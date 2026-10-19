# Аудит v2 — 19.10.2026

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
- doctype `<!DOCTYPE html>` вместо `<! do type>` (standards mode)
- CSS-пути без точки после аллиаса (`www.` → `www`)

### JS / visuo (M1)
- Новый `engine_visuo.js` — единый контроллер `data-action`
- `engine_confidence.js` оставлен, `engine_visuotablebox.js` удалён (мёртвый)
- `visuoel_tabs_page` → `data-action="tabs-show"` (без `onclick`)
- `policy.php` → `data-action="confidence-show"`/`"confidence-hide"`
- `/tests/` — delegate для `tabs-show` (standalone runner)

### Тесты / CI
- `_dev/bootstrap.php`: `VISUO_SRC` → `visuo/sources` (был `engine/visuo/sources`)
- `RouterTest` + `testRootMatch` (A5)
- **436/436** (было 435)
- **`.github/workflows/ci.yml`** — PHPUnit + PHPStan на push/PR

## Открыто (roadmap)

### 🔴 блокеры
- нет (M1 сделан)

### 🟠 важное
- **SSO** — `www` → `visuo\user` (сейчас www всегда гость)
- **PHPStan baseline 439** → Level 6 (стратегически)

### 🟡 мелочи
- `alliasscheme_login.php` — путь `media/js/jsengine/` не существует (404)
- `hert.online`/`idoam.ru` — `onclick` в `policy.php`, `engine_visuotablebox.js` 404 (при активации)
- `engine_visuotablebox.js` — удалён из схем, файл `.js` можно удалить
- `engine_confidence.js` — используется `engine_visuo.js`, оставлен
- `publicsuffix` для `.co.uk` в `domain_for_sso` (сейчас `.co.uk` неверно)
- `__Host-` префикс при HTTPS
- `visuorelease.php` debug `onclick` ×3 — только `GL_DEBUG=true`, dev-only
- `visio` дубли: `www/media/damoony.ru/` vs `shared/` vs `corescripts/templates/media/`
- `db_pass` qwe12345 — сменить до наполнения
- `<doctype>` `< !DOCTYPE html />` — работает, но не валидно (`raw_html` когда-нибудь)

## Файлы изменены (сессия)

- `www/index.php` — Host, cookie, H4, C3
- `engine/middleware.php` — CSP, rateLimit flock
- `engine/cookie.php` — новый класс
- `engine/include.php` — cookie в switchboard
- `engine/library/url.php`, `engine/library/link.php` — U4, B2
- `engine/user.php` — минус мёртвый `login`/`logout`
- `engines/alogic/alogic_bridge.php`, `engines/alogic/Router.php` — A1, A5
- `app/.../alliasglobal.php` ×3 — X1
- `app/.../alliasglobal/policy/policy.php` — `data-action`
- `app/.../alliasglobal/script/headers.php` — language/lang/skin cookie
- `app/.../alliasglobal/bottom/bottom.php` — guard
- `app/.../allias/visuo/user.php` — cookie::set
- `app/.../allias/visuo/dataprovider/dataprovider_base.php` — X2
- `app/.../alliasscheme/alliasscheme_{visuo,www,error}.php` — CSS, JS
- `visuo/sources/visuoel/visuoel_tabs/visuoel_tabs_page.php` — data-action
- `www/media/shared/scripts/engine/engine_visuo.js` — новый
- `www/media/shared/scripts/engine/engine.js` — `visuo: engine_visuo`
- `www/tests/index.php` — delegate tabs-show
- `_dev/bootstrap.php`, `_dev/phpunit/RouterTest.php` — тесты
- `.github/workflows/ci.yml` — новый
- `.gitignore` — `_backup/`, `_tmp/`