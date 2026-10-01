P0-5 закрыт. Все проверки чистые:

- `sources/config.php` — удалён.
- `sources/config/` — удалён.
- `sources/include.php` — упоминаний `config` нет.
- `visuoel.php:42` — комментарий «wrap config», не класс.
- `algoritm_lang_index3.php:68` — текст UI-строки, не код. Файл в аллиасе `www`, под удаление.

Оформляю релиз 2.

---

# Пост-аудит реализации №2

**Дата:** 2026-10-01
**Основание:** `docs/audit-20261001.md`, `docs/audit-20261001-future-1.md`
**Статус:** выполнено (частично)

## Сводка

Закрыты три критичных задачи P0 из плана `audit-20261001-future-1.md`: `error_reporting`, CORS, мёртвый конфиг. Дополнительно удалён мёртвый класс `\config`. Две задачи P0 (`content_html` санитайз, ревизия `sources/user.php`) — по решению владельца отложены.

## 1. `error_reporting` в `www/index.php`

**Проблема:** `Error_Reporting(~E_ALL & ~E_NOTICE)` — побитовое НЕ даёт −32768, `& ~E_NOTICE` фактически отключало показ всех ошибок, включая фатальные.

**Решение:** заменено на `Error_Reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED)`.

**Файл:** `www/index.php`.

## 2. CORS в `damoony\visuo\algoritm::logic()`

**Проблема:** `Access-Control-Allow-Origin` строился из `$_SERVER['HTTP_REFERER']` через `substr($referer, 0, -1)`. При `Allow-Credentials: true` это давало открытый CORS. Заголовок контролируется клиентом.

**Решение:**

- Метод `logic()` вынесен в базовый класс `algoritm_` (`www/logic/alpha.0.0/algoritm_.php`). Любой аллиас получает корректный CORS автоматически.
- Белый список origin'ов формируется из `main_settings->domains_available` + `main_settings->alliases_available` (то есть из `config/site.config`). Единый источник правды.
- `HTTP_REFERER` → `HTTP_ORIGIN` (стандарт для CORS).
- `'*'` и `substr()` удалены.
- Если origin не в белом списке — CORS-заголовки не отправляются, браузер блокирует запрос.
- Переопределение `logic()` в `damoony\visuo\algoritm` удалено.

**Файлы:**

- `www/logic/alpha.0.0/algoritm_.php` — добавлен метод `logic()`.
- `www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/algoritm/algoritm.php` — удалён метод `logic()`.

## 3. Удаление мёртвого конфига `\config`

**Проблема:** класс `\config` и подмодули `config_loader` / `config_define` / `config_settings` применяли конфиг `settings-test.xml.inc` от чужого проекта (`site-def.ru`, `site-cantora.ru`). Файл содержал ~40 констант (`GDEF_*`, `__DB__PREFIX`, `__IMG_*`, `VERSION_*`). Grep подтвердил: ни одна константа в коде проекта не используется.

**Решение:**

- `sources/config/settings-test.xml.inc` удалён.
- Класс `\config` удалён целиком:
  - `sources/config.php`
  - `sources/config/` (каталог: `loader.php`, `define.php`, `settings.php`, `.htaccess`)
- Из `sources/include.php` убран `'config'` из `$sources_modules`.
- Из `www/main.php` убраны: свойство `public config $config`, `new config($this)`, `$this->config->init()`.

**Верификация:**

- `Test-Path sources\config.php` → `False`
- `Test-Path sources\config` → `False`
- `Select-String sources\include.php -Pattern 'config'` → пусто
- `www/main.php` — остались только пути к JSON-файлам (`config/site.config`, `render.config`, `lang.config`), не связанные с классом `\config`.
- `sources/visuo/sources/visuoel.php:42` — комментарий «wrap config», не код.
- `allias/www/algoritm/lang/index/algoritm_lang_index3.php:68` — текст UI-строки, файл в аллиасе `www` (выведен из области аудита).

**Побочный эффект:** `config_define::from_xml()` был единственным потребителем файла, ушёл вместе с классом. Никто другой не вызывал.

## 4. Файлы, затронутые в релизе №2

| Файл | Действие |
|---|---|
| `www/index.php` | правка `error_reporting` |
| `www/logic/alpha.0.0/algoritm_.php` | добавлен `logic()` с белым списком CORS |
| `www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/algoritm/algoritm.php` | удалён `logic()` |
| `sources/config.php` | удалён |
| `sources/config/` (каталог) | удалён |
| `sources/config/settings-test.xml.inc` | удалён (ранее, до релиза №2) |
| `sources/include.php` | убран `'config'` из `$sources_modules` |
| `www/main.php` | убраны `public config $config`, `new config()`, `$this->config->init()` |

## 5. Что осталось в P0

Отложено по решению владельца:

- **P0-3.** Санитайз `content_html` в `algoritm_lang_docs_page.php` и `algoritm_lang_news_page.php`.
- **P0-4.** Ревизия `sources/user.php` (модель с plain-text паролем в cookie + MD5 в `login()`).

Остальные задачи (P1, P2, P3, мёртвый код, перенос под новую структуру `media`/`data`, документация) — в `docs/audit-20261001-future-1.md`.

## 6. Верификация

Выполнено после правок:

- `Test-Path` для удалённых файлов → `False`.
- Grep по `\bconfig\b` → нет ложных срабатываний.
- Grep по `main->config`, `this->config`, `config_settings`, `config_loader`, `config_define` → только собственные определения внутри удалённых файлов (уже нет).

Smoke-тесты `bin/smoke.sh` — прогнать после коммита.

## 7. Приёмка релиза №2

- [x] P0-1 закрыт: `error_reporting` исправлен.
- [x] P0-2 закрыт: CORS через белый список, вынесен в базовый класс.
- [x] P0-5 закрыт: `settings-test.xml.inc` и класс `\config` удалены.
- [ ] P0-3 — отложено.
- [ ] P0-4 — отложено.
- [ ] Коммит и push.