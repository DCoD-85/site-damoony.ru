Отлично, теперь картина полная. Файлы `settings-test.xml.inc` и `subalgoritm/*` **не «неактуальны»** — они в боевом runtime и содержат артефакты других проектов. Это серьёзные находки.

Ниже — **дополнение к `docs/audit-20261001.md`**. Вставляй в соответствующие разделы или сохрани как отдельный файл `docs/audit-20261001-addendum-2.md`. Все блоки markdown-совместимые.

---

# Аудит damoony.ru — дополнение 2

**Дата:** 2026-10-01
**Основание:** присланы `sources/config/settings-test.xml.inc`, `visuo/algoritm/tmp/**`, `visuo/subalgoritm/**`, `visuo/migrations/**`.

## Обновление раздела 1.3.4 (`config.php` / `config_define`)

Ранее я писал «config::init() грузит settings-test.xml.inc — подозрительно, но TODO(audit)». Теперь картина ясна:

**Файл существует и в боевом runtime определяет ~40 констант от чужого проекта.**

`sources/config/settings-test.xml.inc` содержит настройки **старого проекта `site-def.ru` / `site-cantora.ru`**. Через `config_define::from_xml()` эти значения применяются на **каждом запросе**:

Через `define($name, $value)` создаются:

- `GDEF_PRO_INSTALL`, `GDEF_DOMAIN` (пусто), `GDEF_DOMAIN_WEB`, `GDEF_DOMAIN_WEB_IMAGES`, `GDEF_DOMAIN_WEB_AUDIO`, `GDEF_DOMAIN_WEB_VIDEO`, `GDEF_DOMAIN_WEB_DOCUMENTS`, `GDEF_DOMAIN_LOCAL`
- `GDEF_FORMNAME`, `GDEF_USES`, `__DB__PREFIX`, `__ACT__REGS`, `__ACT__AUTH`, `__PROFILE_POST__P_MAX`
- `GDEF_BASENAME`, `GDEF_BASENAME_MASK`, `__USER_ONLINE_TIME_MAX_MIN`, `GDEF_BASEPATH`, `GDEF_LPATH_SITE`, `GDEF_LPATH_IMAGES`
- `GDEF_F_IMAGES_*` (12 констант), `GDEF_F_AUDIO_*` (3), `GDEF_F_VIDEO_*` (3), `GDEF_F_FILES_*` (4)
- `GDEF_FSIZE_IMAGE_MAX_SIZE`, `GDEF_FSIZE_VIDEO_MAX_SIZE`, `GDEF_FSIZE_AUDIO_MAX_SIZE`
- `__IMG_SPEC_REDRAW`, `__IMG__FORMAT`, `__IMG__PROFILE_INFO_AVATAR_MAIN_WIDTH`
- `TEST` (пусто)
- `VERSION_NUM`, `VERSION_START`, `VERSION_UPDATE`, `SITE_START`

**Проблемы:**

1. **Значения от другого проекта:**
   - `GDEF_DOMAIN_WEB = www.site-def.ru`
   - `GDEF_DOMAIN_LOCAL = site-def.ru`
   - `GDEF_BASENAME = Z:\home\site-cantora.ru\www\` — путь Windows для другого проекта
   - `GDEF_LPATH_SITE = site-def.ru\www\`
   - `GDEF_F_AUDIO_LOCAL_PATH = home/site-cantora.ru/www/..audio/`
   - `VERSION_NUM = 1.0.1`, `SITE_START = 01.01.2012`

2. **Двойное хранение:** каждая константа попадает и в `define()`, и в `config_settings::$data`. То есть в памяти висит 40 «мертвых» значений.

3. **Файл называется `-test`**, но работает в проде. Это не «тест», это артефакт.

4. `config_define::from_xml()` не фильтрует невалидные имена. Если когда-нибудь в XML попадёт `name` с пробелом или `$` — `define()` упадёт с warning. Не сейчас, но потенциально.

**Что нужно проверить (grep по всем константам из файла):**

```
grep -rn "GDEF_" sources/ www/ --include="*.php"
grep -rn "__DB__PREFIX\|__ACT__\|__PROFILE_POST__\|__USER_ONLINE_TIME_MAX_MIN" sources/ www/ --include="*.php"
grep -rn "__IMG_" sources/ www/ --include="*.php"
grep -rn "VERSION_NUM\|VERSION_START\|VERSION_UPDATE\|SITE_START" sources/ www/ --include="*.php"
grep -rn "GDEF_F_" sources/ www/ --include="*.php"
```

Если константы **используются** — заменить значения на актуальные для damoony.ru (или создать чистый `settings.xml.inc`).
Если **не используются** — заменить вызов `config->init()` на no-op или удалить файл вместе с вызовом.

## Обновление раздела 4 (роутинг) — баг `algoritm_::include_ext` подтверждён

Из новых файлов видно, почему баг с `$this->algoritm_classes[-1]` вообще возник. В `algoritm_tmp.php` / `algoritm_lang_pagelogo.php` **классы объявлены в глобальном namespace**, а роутер строит класс как `\damoony\visuo\algoritm_tmp`. То есть `class_exists('\damoony\visuo\algoritm_tmp')` вернёт `false` → код падает в `else` → вызывает `$this->algoritm_classes[-1]->release()`. Это и есть источник недоделки.

Сейчас эти файлы не участвуют в роутинге, но **баг остаётся**: если когда-нибудь в `available` попадёт страница с неправильно оформленным классом — 500-я ошибка вместо контролируемого 404.

## Обновление раздела 6 (мёртвый код) — новые находки

### 6.4 Из `visuo/algoritm/tmp/`

**`algoritm_tmp.php`** — мёртв:

- Класс `algoritm_tmp` в глобальном namespace. Роутер строит класс как `\damoony\visuo\algoritm_tmp` — не совпадает. Класс **недостижим**.
- В `release()` есть `print 'OK';` — отладка в боевом коде.
- В `default()` есть ссылки на `$main->alliasglobal->scheme` — такого свойства в `alliasglobal` нет (есть только `policy/top/left/center/right/bottom/headers/head`). Сломанный код.
- `$this->available`, `$this->search`, `$this->default` присваиваются, но не используются в классе — тоже артефакт.

**`pagelogo/algoritm_lang_pagelogo.php`** — мёртв:

- Класс в глобальном namespace — недостижим.
- `default()` делает `header('Content-Type: image/png')`, `readfile('D:\OSPanel543\domains\damoony2.ru\www\mediaoxydog\avatars\1\fon\1.png')`, `die($path)`. **Жёсткие пути для локальной Windows-машины разработчика.** При любом запуске на боевом — провал.
- Ссылается на `$main->alliasscheme` как на объект со свойствами (`$scheme = $main->alliasscheme` — но потом не используется). Пустой «гвоздь».
- Вызывает `$left->showasmenu( false )` — такого метода нет в `alliasglobal_left` (там `asmenu()`).

### 6.5 Из `visuo/subalgoritm/` — весь каталог

**Ни один файл не участвует в боевом runtime.** `main.php` не содержит свойства `public subalgoritm $subalgoritm`. `visuo/algoritm/algoritm.php` не создаёт `subalgoritm`. `visuo/include.php` его не подключает.

Что там:

- **`subalgoritm.php`** — класс `subalgoritm` в глобальном namespace. `autoload()` подключает `top.php`/`left.php`/`right.php`/`bottom.php` **относительными путями** без `__DIR__`. Даже если бы вызывалось — упало бы, если CWD не тот.
- **`bottom.php`, `left.php`, `right.php`** — классы `subalgoritm_bottom/left/right`, содержимое **идентично** (копипаста × 3): один и тот же текст «Политика конфиденциальности DAMOONY» на ~30 строк HTML. Явно тестовый placeholder.
- **`top.php`** — switch по `$url->url()[1]` со значениями `'index.php'`, `'tests.php'`, `'login.php'`. Это **URL-схема от старой архитектуры** (когда роутинг был `index.php`). На текущих URL (`/ru-ru/...`) первым сегментом всегда будет `ru-ru`, никогда `index.php`. **Switch не срабатывает никогда.** Внутри — длинная тестовая структура меню (`name1`, `name2`, `name3`, «Это первое, и не единственное меню»).
- **`topmenu.php`** — `class topmenu` с методом `start()`, который ничего не делает.
- **`index.php`** — пустой файл.

**Итого: 7 файлов, ~500 строк мёртвого кода.**

## Обновление раздела 9 (БД) — миграции

Все 7 миграций + `apply_all.sql` существуют и совпадают со схемой `visuo_damoony` в phpMyAdmin. **Drift схемы отсутствует** — это хорошо.

Замечания:

1. **`001_create_news.sql`** создаёт `idx_published(status, published_at)` **и** `idx_lang_status_published(lang, status, published_at)`. Первый — префикс второго без `lang`. Для запросов с `lang` используется второй; для запросов без `lang` — первый. В `dataprovider_news` все запросы идут с `lang`, значит `idx_published` не используется. **Кандидат на удаление** (уже отмечено в 7.3).

2. **`004_alter_users_timezone_lang.sql`** — комментарий: «MySQL 8 не поддерживает ADD COLUMN IF NOT EXISTS». Проект на **MariaDB 11.8**, где это поддерживается с 10.0. Комментарий скопирован из чужого окружения. Функционально миграция работает, но документация вводит в заблуждение.

3. **`007_create_login_attempts.sql`** — комментарий: «5 неудачных за 15 минут → блок IP на 15 минут». Реализация в `dataprovider_user::check_bruteforce()` блокирует по `login OR ip` (не только IP). **Расхождение комментария и кода.**

4. **`apply_all.sql`** использует `SOURCE <file>;` — это синтаксис MySQL CLI, **не работает в phpMyAdmin**. Если владелец применял через phpMyAdmin — файл не сработал бы. Указывать в документации: применять по одному или через `mysql < apply_all.sql`.

5. **`005_seed_files.sql`** — `filesize = 0`, требует ручного `UPDATE` после установки архива. Это ок, но лишний шаг.

## Обновление раздела 10.1 (что чистить)

Добавить:

- `visuo/algoritm/tmp/algoritm_tmp.php` — мёртв.
- `visuo/algoritm/tmp/pagelogo/algoritm_lang_pagelogo.php` — мёртв.
- `visuo/subalgoritm/*` — 7 файлов, весь каталог.

## Обновление раздела 10.2 (что улучшить)

Добавить:

**Конфиги (P1):**

- `sources/config.php::init()` — заменить вызов `from_xml('settings-test.xml.inc')` на актуальный конфиг `damoony.ru` или удалить вызов. **Предварительно — grep по всем константам из файла** (список выше). Если константы не используются — удалить файл.

**Миграции (P2):**

- Привести комментарии в `004_alter_users_timezone_lang.sql` и `007_create_login_attempts.sql` в соответствие с реальностью.
- В `apply_all.sql` добавить предупреждение, что `SOURCE` работает только в CLI, не в phpMyAdmin.

**БД (P2):**

- Удалить `news.idx_published` (подтверждено миграцией — он избыточен).

## Обновление раздела 10.5 (issues)

**Добавить:**

**P1:**

- `sources/config.php::init()` грузит `settings-test.xml.inc` — конфиг от чужого проекта (`site-def.ru`, `site-cantora.ru`), значения которого применяются в боевом runtime.

**P2:**

- `algoritm_::include_ext` — подтверждён источник бага (неправильный namespace в старых файлах).

**P3:**

- Комментарии в миграциях `004` и `007` не соответствуют реальности.

## Обновление раздела 10.6 (оценка)

Ничего не меняется — общая оценка **остаётся 7.5 / 10**. Новые находки укладываются в уже существующие категории («мёртвый код», «безопасность»), просто уточняют масштаб.

Мёртвый код вырастает с ~10% до **~13%** (subalgoritm + tmp добавляют ~500 строк). Это по-прежнему ниже порога, влияющего на оценку.

## Что ещё нужно проверить

**grep по константам из `settings-test.xml.inc`.** Это единственный оставшийся вопрос, влияющий на вывод: используются ли эти 40 констант в коде, или они полностью мертвы.

Если хочешь — выполни сам:

```
cd E:\OSPanel543\domains\damoony2.ru
findstr /S /I /M "GDEF_" sources\*.php www\*.php www\logic\alpha.0.0\*.php www\logic\alpha.0.0\domain\damoony.ru\*.php www\logic\alpha.0.0\domain\damoony.ru\allias\visuo\*.php www\logic\alpha.0.0\domain\damoony.ru\allias\visuo\**\*.php
```

или через PowerShell:

```powershell
Get-ChildItem -Path sources,www -Recurse -File -Include *.php |
  Select-String -Pattern 'GDEF_|__DB__PREFIX|__ACT__|__IMG_|VERSION_NUM|VERSION_START|SITE_START|GDEF_F_' |
  Select-Object Path, LineNumber, Line
```

Пришли результат — допишу окончательную формулировку про `settings-test.xml.inc` в раздел 6 (мёртвый код) и раздел 10.1 (что чистить).
