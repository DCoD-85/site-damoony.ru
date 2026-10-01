# Пост-аудит: план работ (future 1)

**Дата:** 2026-10-01
**Основание:** `docs/audit-20261001.md`, `docs/audit-20261001-released-1.md`
**Статус:** к выполнению

Список задач, выявленных в ходе аудита и не вошедших в релиз №1. Сгруппированы по приоритету. Порядок внутри группы — не задаёт последовательность.

## P0 — критические

### Безопасность

- `error_reporting(~E_ALL & ~E_NOTICE)` в `www/index.php` — фактически отключает показ всех ошибок. Заменить на корректную маску.
- CORS в `damoony\visuo\algoritm::logic()` — строится из `$_SERVER['HTTP_REFERER']`. Заменить на белый список доменов.
- `content_html` в `algoritm_lang_docs_page` и `algoritm_lang_news_page` — рендерится без санитайза. Определить политику: санитайз на входе (`article_create`/`article_update`) или на выходе.
- `sources/user.php` — модель с plain-text паролем в cookie + сравнение в SQL без `password_hash`, `md5()` в `login()`. После удаления `login_damoony` вызов отключён в `main::init()`, но код остаётся. Ревизовать.

### Схема БД

- `sources/config/settings-test.xml.inc` — конфиг от чужого проекта (`site-def.ru`, `site-cantora.ru`) применяется в боевом runtime через `config::init()`. Содержит ~40 констант (`GDEF_*`, `__DB__PREFIX`, `VERSION_NUM` и др.). Требуется grep по константам, затем либо замена на актуальный конфиг, либо удаление вызова.

## P1 — высокий

### Безопасность

- `setcookie` в `alliasglobal_script_headers::handle_language()` и `handle_skin()` — без `SameSite`, `HttpOnly`.
- `dataprovider_files::create` — `mime_type` не валидируется по whitelist.
- `main::init()` — `connect_type = 'http://'` жёстко. Проверить поведение при HTTPS-боевом.
- `$_COOKIE['render'/'language'/'skin']` без `??` в `main_settings::__construct`.
- `$_GET['phpinfo']` без `isset()` в `www/index.php::error()`.
- `defined(TRUE)` вместо `defined('CONST')` в `www/index.php` — «защита» не работает.
- `main::logic()` создаёт классы из URL без `class_exists()` — неконтролируемый 404.

### Логика

- `algoritm_::include_ext` — при `class_exists($class) === false` вызывается `$this->algoritm_classes[$url_base_pos - 1]->release()`. При `$url_base_pos = 0` — fatal.
- `main::redirect()` — печатает `<div>REDIRECT: ...</div>` перед логикой. Отладочный вывод в проде.
- `visuo\algoritm_lang_profile` — при неверном CSRF форма перерисовывается молча, без сообщения об ошибке.

### Производительность

- N+1 в `alliasglobal_right_visuo.php::menu_docs` — `articles_by_section` в цикле по `sections_tree`.
- N+1 в `algoritm_lang_docs.php::render_list` — то же.
- `dataprovider_doc::sections_tree()` — без LIMIT.
- `dataprovider_files::all_active()` — без LIMIT.

## P2 — средний

### Схема БД

- `visuo_damoony.news.idx_published` — дублирует префикс `idx_lang_status_published`. Удалить.
- `visuo_damoony.file_downloads.idx_file` — заменить на составной `(file_id, created_at)` для `downloads_stats`.
- `visuo_damoony.login_attempts::check_bruteforce()` — запрос с `OR` мешает индексам. Добавить `idx_success_created(success, created_at)` или переписать запрос.

### Миграции

- `004_alter_users_timezone_lang.sql` — комментарий про MySQL 8 не соответствует MariaDB 11.8. Привести в порядок.
- `007_create_login_attempts.sql` — комментарий «блок IP на 15 минут» не соответствует коду (`login OR ip`).
- `apply_all.sql` — использует `SOURCE`, который работает только в CLI. Отметить в документации.

### Архитектура

- `alliasscheme_*` — 5 файлов с ~300 строками copy-paste (`meta/body_preload/clear/init/refresh/load/reload`). Вынести базовый класс.
- UI-движок — параллельно живут `visuoelement` + `visuoel`. Определить судьбу каждого.
- `main::init()` — открытие `login_damoony` и `user->authorized()` больше не нужно (см. релиз 1). Убрать.

### Конфиги

- `render.config` — ключи `default/guest/auth/bot` не используются в `main_settings`.
- `lang.config` — `default/list/path` не используются в `main_settings`.
- `site.config` — `title` не обрабатывается.

## P3 — низкий

- `library_link::ahref()` — нет `urlencode` для query-параметров.
- `gmod::show_list()` — `$return .= ` без инициализации (warning на PHP 8.4).
- `library_gen::id()` — `rand()` вместо `random_int()`.

## Мёртвый код — к удалению

### Ядро

- `sources/system.php` + свойство `public system $system` в `main.php`.
- `sources/mod.php` + `sources/mod/index.php`.
- `sources/gmod/test/`, `sources/gmod/football/`, `sources/gmod/user/` — тестовые заглушки.
- `sources/vardump.php::my_vardump()` — дубль `vardump()`.
- `sources/config/loader.php::read_txt()` — не вызывается.
- `sources/log.php::astag()`, `log_row::astag()` — alias.
- `sources/db/mysqli/query.php::insert_legacy()` — legacy, проверить.
- `sources/visuo/sources/visuodata.php` — дубль `visuoattrsdata.php`.
- `sources/visuo/sources/visuorelease.php::release_min/release_struct` — заглушки.
- `sources/visuo/sources/visuotag.php::tree/before/after/prepare_visuotag/prepare_visuostruct` — пустые.
- `sources/include.php` — fallback в `sources_old/`.

### Точка входа

- `www/include.php` — полный дубль `www/included.php` + `include_test()`.
- `www/main.php::main_settings::alliases()` — пустое тело.
- `www/main.php::main_settings::init()` — `$v = new visuotag()` не используется.
- `www/index.php` — `SCRIPTSTART_MT`, `SCRIPTSTART_HR` не читаются.
- `www/index.php::warning_file()` — тело закомментировано.

### Аллиас visuo

- `visuo/algoritm/tmp/algoritm_tmp.php` — класс в глобальном namespace, недостижим роутером; `print 'OK'` в `release()`; ссылки на несуществующие свойства.
- `visuo/algoritm/tmp/pagelogo/algoritm_lang_pagelogo.php` — жёсткие Windows-пути, недостижим.
- `visuo/algoritm/tmp/` — весь каталог.
- `visuo/subalgoritm/` — весь каталог (7 файлов, ~500 строк): классы `subalgoritm_bottom/left/right` содержат идентичный тестовый текст; `top.php` переключается по старым URL (`index.php`, `tests.php`, `login.php`), которые больше не встречаются; `topmenu.php` — пустой; `index.php` — пустой.
- `visuo/algoritm/algoritm_pageshablon.php` — ревизовать (не подтверждено использование).

## Правки под новую структуру media / data

Выполнено в релизе №1: создана новая структура `www/media/` и `data/`.

Осталось (перенос кода):

- `www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/include.php` — константы `VISUO_MEDIA_DOWNLOADS`, `VISUO_MEDIA_IMG` → новые пути (`/data/...`, `/www/media/...`).
- `www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/algoritm/lang/download/page/algoritm_lang_download_page.php` — путь к файлам.
- `www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/dataprovider/dataprovider_files.php` — путь к файлам.
- `sources/messages.php` — путь к иконкам `close.png`/`full.png`.
- `alliasscheme_error.php`, `alliasscheme_login.php`, `alliasscheme_min.php`, `alliasscheme_visuo.php`, `alliasscheme_www.php` — хардкоды `media/release/<skin>/...` в `meta()`.
- `www/index.php::error()` — ссылка на `media/css/main.css`.
- `www/logic/alpha.0.0/include.php` — если определяет пути к статике.

Осталось (перенос файлов):

- Ресурсы из `media_old/` (уже удалена) — восстановить из Git-истории и разложить по новой структуре.

Осталось (инфраструктура):

- `index.php` заглушки и `.htaccess` в новых папках `media/**`.
- `.htaccess` в `media/` с `Options -Indexes`.
- `.gitkeep` для пустых папок.

## Документация

- `docs/security.md` — правила: пароли только `password_hash`, формы только с `csrf_check()`, вывод через `h()`, `content_html` только после санитайза, SQL только через `%s`/`%d`, path traversal через `basename+realpath+str_starts_with`.
- `docs/database.md` — карта БД после чистки.
- `docs/architecture.md` — дополнить разделом про роутер `algoritm_` и regex-паттерны.
- `docs/media-structure.md` — новая структура `www/media/` и `data/` (по релизу №1).
- `CHANGELOG.md` — записи про: исключение `www`/`login`/`login_damoony` из области аудита; перестройку `media/`.

## Приёмка

- [ ] P0 закрыты
- [ ] P1 закрыты
- [ ] Мёртвый код удалён
- [ ] Код переведён на новую структуру `media`/`data`
- [ ] Документация обновлена
- [ ] Коммит и push