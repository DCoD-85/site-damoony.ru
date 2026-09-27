# Манифест проекта Damoony2

**Передача следующему агенту. Составлено по итогам сессии.**

---

## 1. Цель проекта

Строим **скелет (фабрику) сайта**, а не готовый сайт. Наполнение появится позже. Задача — дать программисту **инструменты**: правила, контракты, механизмы, с помощью которых он наполнит сайт.

**Ключевые принципы:**
- Программист реализует **через наши правила**, не «как получится».
- Скелет работает без наполнения.
- Наполнение — слой поверх скелета.
- Минимум `if/else` в логике — за счёт разделения ответственности.

**Технический стек:** PHP 8.4, Apache 2.4.68, MariaDB, Debian 13 (Trixie). Без фреймворков. Свой switchboard, свой UI-движок (visuo / visuoel / visuotag).

---

## 2. Два окружения

### 2.1. Локальная разработка (основная работа)

**Путь:** `E:\OSPanel543\domains\damoony2.ru`

**OSPanel 5.4.3** (Windows), домен `damoony2.ru`:
- **PHP 8.3** (проверить в OSPanel)
- **Apache 2.4.x**
- **MySQL/MariaDB** (локально)
- **Хосты:** в `C:\Windows\System32\drivers\etc\hosts`:
  ```
  127.0.0.1 damoony2.ru
  127.0.0.1 www.damoony2.ru
  127.0.0.1 login.damoony2.ru
  ```

**Доступ:** `http://damoony2.ru/`
**Тесты:** `http://damoony2.ru/tests/` (табы с iframe по каждому `visuo*.php`)

**Важно:**
- **OPcache** — после правки классов: Restart OSPanel.
- **`secure/main_settings.config`** — локально домены и аллиасы для `damoony2.ru` мапятся на `damoony.ru` (см. `domains_real` в конфиге).
- **Логи Apache/PHP:** `E:\OSPanel543\modules\logs\Apache-PHP-8.3\error.log`.

**Git:** `git@github.com:DCoD-85/site-damoony.ru.git` (ветка `main`).

### 2.2. Боевой сервер (для проверки)

**IP:** 188.187.1.104 (статический, за NAT, внутренний 192.168.1.101)
**Домены:** `damoony.ru`, `idoam.ru` — оба A-записи на этот IP.
**Wildcard DNS:** `*.damoony.ru` — работает.
**SSH:** `root@188.187.1.104`

**Всё см. раздел 11 (серверная инфраструктура).**

---

## 3. Структура проекта

### 3.1. Корень проекта (локально и на сервере — одинаково)

```
{project-root}/
├── .gitignore
├── .vscode/
├── _archive/                   ← архив старых версий (не трогать)
├── _audit/                     ← документация проекта
├── _backup/                    ← слепки (в .gitignore)
├── secure/                     ← ВНЕ DocumentRoot — конфиги и секреты
│     └── main_settings.config  ← JSON: db, domains, alliases, render
├── sources/                    ← ядро (PHP)
│     ├── include.php           ← switchboard — грузит модули
│     ├── config.php, core.php, db.php, ...
│     └── visuo/                ← UI-подсистема
│           ├── visuo.php       ← switchboard модуля
│           ├── css/            ← (устаревшая, удаляется)
│           └── sources/
│                 ├── visuotag.php            ← HTML-строитель (основа всего)
│                 ├── visuorelease.php        ← рендер HTML / JSON
│                 ├── visuostruct.php         ← фасад структур
│                 ├── visuostruct/
│                 │     ├── visuostruct_min.php
│                 │     └── visuostruct_ajax.php
│                 ├── visuometa.php           ← meta для <head>
│                 ├── visuoelement.php        ← старый движок UI
│                 ├── visuoelements.php       ← switchboard старого движка
│                 ├── visuoelements/          ← старые UI-классы
│                 ├── visuoel.php             ← новый движок UI (база)
│                 ├── visuoels.php            ← switchboard нового движка
│                 └── visuoel/                ← новые UI-классы
│                       ├── visuoel_form/, visuoel_tabs/
│                       ├── visuoel_input/, visuoel_field/
│                       ├── visuoel_button/, visuoel_block/
│                       ├── visuoel_message/, visuoel_table/
│                       └── visuoel_group.php, visuoel_group_element.php
│                       (visuostruct_view.php — НЕ СОЗДАН, обсуждался)
└── www/                        ← DocumentRoot
      ├── .htaccess             ← рерайтит всё на index.php
      ├── index.php             ← точка входа
      ├── include.php, included.php
      ├── constants.php, main.php
      ├── logic/                ← бизнес-логика по доменам
      │     └── alpha.0.0/
      │           ├── include.php
      │           ├── algoritm_.php           ← база алгоритмов
      │           ├── algoritmdata_.php       ← data для алгоритма
      │           ├── domain/
      │           │     └── damoony.ru/
      │           │           ├── allias/     ← аллиасы
      │           │           │     ├── www/  ← домен www
      │           │           │     │     ├── include.php
      │           │           │     │     └── algoritm/
      │           │           │     │           ├── algoritm.php
      │           │           │     │           ├── algoritm_auth.php
      │           │           │     │           ├── algoritm_error.php
      │           │           │     │           └── lang/
      │           │           │     │                 ├── algoritm_lang.php
      │           │           │     │                 ├── index/
      │           │           │     │                 ├── form/
      │           │           │     │                 ├── login/
      │           │           │     │                 ├── ...
      │           │           │     ├── login/        ← домен login
      │           │           │     ├── install/, visuo/, football/ ...
      │           │           │     └── _shablon/     ← шаблон
      │           │           ├── allias.php          ← фасад аллиаса
      │           │           ├── alliasglobal.php    ← наполнение слоёв
      │           │           ├── alliasglobal/       ← файлы слоёв
      │           │           │     ├── top/  (top.php, top_www.php, ...)
      │           │           │     ├── left/ (left.php, left_www.php, ...)
      │           │           │     ├── center/
      │           │           │     ├── right/
      │           │           │     ├── bottom/
      │           │           │     ├── menu.php
      │           │           │     ├── headers.php
      │           │           │     ├── auth.php
      │           │           │     └── scheme.php
      │           │           ├── alliaslocal.php     ← локальные переопределения
      │           │           ├── alliaslocal/        ← top.php, bottom.php
      │           │           └── alliasscheme.php    ← схемы страниц
      │           │                 └── alliasscheme/
      │           │                       ├── alliasscheme_login.php
      │           │                       ├── alliasscheme_error.php
      │           │                       ├── alliasscheme_min.php
      │           │                       └── alliasscheme_www.php
      │           └── (для других доменов — своя подпапка)
      ├── language/
      ├── logic/  ← см. выше
      ├── media/                  ← PUBLIC ассеты
      │     ├── css/, img/, js/, fonts/
      │     ├── release/default/  ← собранные (release)
      │     │     ├── css/
      │     │     ├── js/
      │     │     └── visuo/
      │     │           ├── visuo.css
      │     │           ├── visuo-elements-*.css
      │     │           └── visuoel/
      │     │                 ├── visuo-elements-field.css
      │     │                 ├── visuo-elements-table.css
      │     │                 ├── visuo-elements-tabs.css
      │     │                 ├── visuo-elements-buttons.css
      │     │                 ├── visuo-elements-block.css
      │     │                 └── visuo-elements-input.css
      │     ├── js/jsengine/
      │     │     ├── engine.js               ← фасад
      │     │     ├── engine_load.js          ← min-запросы
      │     │     ├── engine_page.js          ← ajax-запросы
      │     │     ├── engine_prepare.js       ← подготовка форм
      │     │     ├── engine_popup.js         ← попапы
      │     │     ├── engine_ready.js         ← DOM ready + инициализация
      │     │     ├── engine_confidence.js    ← соглашение
      │     │     ├── engine_visuotablebox.js
      │     │     └── messages.js
      │     └── favicon.ico
      └── tests/                  ← тесты (http://damoony2.ru/tests/)
            ├── index.php         ← раннер: табы с iframe
            ├── _vt.php           ← bootstrap для тестов
            ├── visuoel_table.php
            ├── visuoel_field_wrap.php
            ├── visuoel_form.php
            ├── visuoel_fieldset.php
            ├── visuoel_button.php
            ├── visuoel_input.php
            ├── visuoel_block.php
            ├── visuoel_field.php
            ├── visuoel.php
            ├── visuotag.php
            ├── visuoattrs*.php
            └── ...
```

### 3.2. Что где лежит (принципы)

| Папка | Что там | Кто правит |
|---|---|---|
| `sources/` | Ядро, switchboard, модули | Редко (основа) |
| `sources/visuo/sources/visuoel/` | UI-компоненты (form, table, button, …) | Часто (новые фичи) |
| `www/` | Публичный корень (DocumentRoot) | Точка входа |
| `www/logic/alpha.0.0/domain/damoony.ru/allias/` | Логика аллиасов (www, login, …) | Часто (алгоритмы) |
| `www/logic/alpha.0.0/domain/damoony.ru/alliasglobal/` | Наполнение слоёв страницы | Часто |
| `www/logic/alpha.0.0/domain/damoony.ru/alliasscheme/` | Каркасы страниц (HTML) | Редко |
| `secure/` | Секреты, конфиги | Только админ |
| `www/media/release/` | Собранные CSS/JS | Автогенерация |
| `www/tests/` | Тесты | При каждой фиче |

### 3.3. Логика доменов и аллиасов

```
www/
  logic/alpha.0.0/
    domain/
      damoony.ru/                 ← домен
        allias/                   ← аллиасы этого домена
          www/                    ← default аллиас
            algoritm/             ← алгоритмы для www
          login/                  ← аллиас login
          ...
      idoam.ru/                   ← другой домен (своя структура)
```

**URL `/ru-ru/login/form/`** → алгоритм `algoritm_lang_login` (или `algoritm_lang_form`) внутри `allias/www/algoritm/lang/`.

**URL `/ru-ru/registration/`** на домене `login.damoony.ru` → алгоритм в `domain/damoony.ru/allias/login/algoritm/lang/registration/`.

### 3.4. Как работает switchboard

`sources/include.php` грузит модули в порядке:
```
render, user, blacklist, vardump, log, core, config, db, library,
system, language, messages, visuo, gmod, mod
```

`sources/visuo/visuo.php` внутри грузит `sources/visuo/sources/visuoels.php`, тот — все visuoel-классы в правильном порядке (базы → элементы → группы).

`www/index.php` → `www/include.php` → `www/included.php` → `www/main.php`.

---

## 4. Состояние на момент передачи

### 4.1. Что реализовано

- **visuotag** — HTML-строитель (основа всего).
- **visuoel** — UI-движок: form, tabs, input, field, button, block, message, table.
- **visuostruct_min** / **visuostruct_ajax** — структуры для JSON.
- **alliasscheme_*** — схемы страниц (login, error, min, www).
- **alliasglobal_*** — наполнение слоёв (top, left, center, right, bottom).
- **alliaslocal_*** — локальные переопределения для аллиасов.
- **JS-движок:** `engine_load.js` (min), `engine_page.js` (ajax), `engine_popup.js`, `engine_ready.js`, `messages.js`.
- **Почта:** не настроена.
- **Рендер:** работает через `visuorelease::final()`, флаг `min` (bool).

### 4.2. Что согласовано, но не реализовано

- **`render_display`** (`static` | `dynamic`) в `main_settings`.
- **Resolver** режима: profile (bot/human), access (guest/auth), js (on/off).
- **Единый `visuorelease::final()`** — читает `render_display`, решает что печатать.
- **`visuostruct_view`** — единая структура от алгоритма (обсуждалась).
- **Миграция слоёв на visuoel** — обсуждалось, но не начато.

### 4.3. Что решено НЕ менять

- **Структуры** `visuostruct_min` / `visuostruct_ajax` — работают, оставить.
- **Схемы** `alliasscheme_*` — работают.
- **Слои** `alliasglobal_*` — работают.
- **JSON-форматы** — фиксированы JS, не менять без синхронного обновления JS.

---

## 5. Архитектура (согласовано)

### 5.1. Три уровня абстракции

```
СХЕМА (scheme)      — каркас HTML: <html><body><div class="dt">…
                       Слоты: top / left / center / right / bottom
                       Работает через visuotag (не visuoel)

СЛОЙ (alliasglobal) — наполнение слота (top, center, …)
                       Своя логика: откуда данные (файл / БД / API)
                       Своя разметка
                       Может переопределяться аллиасом (alliaslocal)

СТРУКТУРА (visuostruct) — формат ответа:
                       full    → HTML (схема + слои)
                       min     → JSON с одним слоем + мета
                       ajax    → JSON со всеми слоями + мета
```

### 5.2. Режимы отображения

**Два базовых режима:** `static` (полный HTML) и `dynamic` (HTML с разметкой + AJAX-подгрузки).

**Внутри `dynamic` — два формата:**
- `min` — короткий JSON: `{ error, messages, release: { html, layer, popup }, form, referer }`
- `ajax` — JSON со всеми слоями: `{ error, address, layers: { top: {layer, html}, ... } }`

Это детализация, не отдельные режимы.

### 5.3. Определение режима (приоритеты)

Resolver (`main_settings->resolve_render()`):
1. **бот** → `static`
2. **js off** → `static`
3. **`?display=static|dynamic`** → явный выбор
4. **`$_SESSION['render_display']`** → сохранённый выбор
5. **`$render[ $access ]['display']`** → по доступу (guest/auth)
6. **`$render['default']['display']`** → дефолт

**Access:** `guest` | `auth` — по `user->is_guest()`.
**Bot:** по User-Agent.
**JS:** по куке `js_enabled` (ставится JS-движком) или оптимистично `on`.

---

## 6. Задачи

### Приоритет 1 — Настройки (первый шаг)

**1.1. Добавить секцию `render` в `secure/main_settings.config`:**
```json
"render": {
  "default": { "display": "dynamic" },
  "guest":   { "display": "static" },
  "auth":    { "display": "dynamic" },
  "bot":     { "display": "static" }
}
```

**1.2. Свойства в `main_settings`** (`www/main.php`, класс `main_settings`):
```php
public array  $render         = [ ... ];   // из конфига
public string $render_display = 'static';  // вычисленный
public string $render_profile = 'human';   // bot | human
public string $render_access  = 'guest';   // guest | auth
public bool   $render_js      = true;
```

**1.3. Методы resolver** (в `main_settings`, вызывать из `main::logic()`):
- `resolve_render()` — заполняет `render_*`.
- `render_detect_bot()` — по UA.
- `render_detect_js()` — по куке `js_enabled`.
- `render_pick_display()` — по приоритетам выше.

**1.4. Кука `js_enabled`** — JS-движок ставит при первой загрузке. Сейчас не реализовано; resolver оптимистично считает `on`.

### Приоритет 2 — visuorelease

**2.1. Метод `display()`** — читает `render_display` из `main_settings`.

**2.2. Единый `final()`** — заменить существующую логику (`render->getdata('min')`):
```php
final():
  display = main_settings->render_display
  is_ajax = is_ajax_request()

  if is_ajax:
      print release_alternative_value()
      return

  if display == 'dynamic':
      html->data_add('ajax', true)
      html->data_add('ajax-init', false)

  print release()
```

**2.3. `is_ajax_request()`** — по заголовку `X-Requested-With: XMLHttpRequest`, параметру `?_mode=min|ajax`, или `X-Render-Mode`.

**2.4. Удалить `final_ajax()`** — поглощается `final()`.

**2.5. Опционально:** `release_alternative` сделать массивом (вместо строки JSON).

**2.6. Совместимость:** `release_min()` / `release_struct()` оставить как обёртки — старые алгоритмы продолжат работать.

### Приоритет 3 — Миграция слоёв на visuoel (поэтапно)

**3.1. Пилот: `layer_bottom`** — переписать `alliasglobal_bottom` как `visuoel_group_element`. Проверить, что рендерится идентично.

**3.2. Ещё пара слоёв** — `layer_left`, `layer_right`.

**3.3. Сложные слои** — `layer_top`, `layer_center`.

**3.4. Схемы** — только если этапы 3.1–3.3 покажут, что visuoel справляется.

**Принцип:** схема — visuotag (каркас), слой — visuoel (компонент), JSON — отдельный формат, resolver — отдельный класс.

### Приоритет 4 — Почта

**Не сделано.** Требуется:
- Внешний SMTP-релей (Яндекс 360 для бизнеса / Brevo / Mailgun).
- SMTP-API для отправки из PHP.

**Ограничение:** локальный MTA не работает — IP в Spamhaus PBL (residential).

### Приоритет 5 — SSL для `idoam.ru`

Wildcard `*.damoony.ru` не покрывает `idoam.ru`. Нужен отдельный сертификат или multi-domain.

---

## 7. Правила взаимодействия с пользователем

1. **Полные листинги файлов**, не фрагменты.
2. **Один шаг — одна проверка.** Код → `php -l` → Restart OSPanel → браузер → коммит.
3. **Тесты — отдельным файлом** в `www/tests/<module>.php`. Формат: `<h2>N. Название</h2>` + `<b>значение</b> (ожидаем X)`. В конце `echo '<hr>done';`.
4. **Один этап — один коммит.**
5. **Не пихать в один коммит разные фичи.** Table-семейство отдельно, field-семейство отдельно, регистрация отдельно.
6. **Флажки / риски** в конце каждого шага.
7. **OPcache:** после правки классов — Restart OSPanel.
8. **Пользователь копирует код вручную** через редактор.
9. **Не «успокаиваться»** — если пользователь говорит «это наша задача», коммитить.

---

## 8. Стиль кода

- **Табы**, не пробелы.
- **`<?PHP`** — заглавные.
- **`# --- state ---`** / **`# --- methods ---`** — секции.
- **Nullable типы:** `null|X`, `null|string`, `null|visuoel__data`.
- **Возвращаемые типы:** `: void`, `: string`, `: int`, `: static`, `: bool`.
- **`??=`** — для lazy-init.
- **Никаких dynamic properties** (PHP 8.3+ выдаёт `Deprecated`).
- **Ранний возврат** для пустых случаев.
- **`->` через пробелы:** `$obj->method ( $arg )`.
- **Выравнивание присваиваний** по колонкам, если группа строк.

---

## 9. Тесты (регрессия)

**Локально:** `http://damoony2.ru/tests/`

Прогнать после любых правок:
- `visuoel_table.php`
- `visuoel_field_wrap.php`
- `visuoel_form.php`
- `visuoel_fieldset.php`
- `visuoel_button.php`
- `visuoel_input.php`
- `visuoel_block.php`
- `visuoel_field.php`
- `visuoel.php`

**Ожидание:** ни fatal, ни Deprecated, ни Warning. В конце каждого — `done`.

---

## 10. Известные подводные камни

- **PHP covariance return types:** при объявлении группы PHP проверяет return-типы. Если класс из return-типа ещё не объявлен — fatal. **Порядок include: базы → элементы → группа.**
- **`visuotag::tag()` возвращает новый узел**, не `$this`. Всегда `$vt = $vt->tag( ... )`.
- **`visuotag::content()` не экранирует.** Для текста — `htmlspecialchars( $v , ENT_QUOTES )`.
- **`visuoattrs::add()` пропускает пустые значения.** Для `value=""` нужен третий параметр `true`.
- **`add()` на visuoel** принимает `visuoel|visuotag`. Плейсхолдер разворачивается в `init()`.
- **Инициализация идемпотентна** через флаг `$init`. Двойной вызов `init()` не должен запускать `build()` снова.
- **Порядок вызовов в cell:** `set()` и `add()` — порядок сохраняется через `$elements[]`.
- **`add()` гарантирует одного родителя** — снимает с прежнего при переносе.
- **Не использовать `git add .`** — много рабочих файлов.

---

## 11. Серверная инфраструктура (боевой)

### 11.1. Общее

- **IP:** 188.187.1.104 (статический, за NAT; внутренний 192.168.1.101)
- **ОС:** Debian 13 (Trixie)
- **Apache:** 2.4.68 + PHP 8.4 (mod_php)
- **SSH:** `root@188.187.1.104`

### 11.2. Домены и SSL

- `damoony.ru`, `idoam.ru`, wildcard `*.damoony.ru` → A-запись на 188.187.1.104
- **Wildcard SSL:** `*.damoony.ru`, действует до 12.04.2027
  - `/etc/ssl/damoony-wildcard/damoony.ru.crt`
  - `/etc/ssl/damoony-wildcard/damoony.ru.key`
  - `/etc/ssl/damoony-wildcard/damoony.ru-ca.crt`
- **`idoam.ru`** — без HTTPS (нужен отдельный сертификат).

### 11.3. Apache

- **Vhost:** `/etc/apache2/sites-available/damoony.ru.conf`
  - Блок `*:80` — редирект на HTTPS (кроме `/.well-known/`)
  - Блок `*:443` — SSL, `DocumentRoot /var/www/damoony.ru/www`
  - `ServerAlias www.damoony.ru *.damoony.ru idoam.ru www.idoam.ru *.idoam.ru`
- **`000-default.conf`** — отключён (`a2dissite 000-default`), не включать обратно.
- **Логи:** `/var/log/apache2/damoony.ru-error.log`

### 11.4. Деплой

```
/var/www/damoony.ru/
├── www/           ← DocumentRoot (git clone сюда)
├── sources/       ← ядро (родитель www)
├── secure/        ← конфиги
├── ssl/           ← CSR + KEY (не сертификаты)
├── www2.zip … www6.zip   ← бэкапы сайта (не удалять)
```

### 11.5. База данных

MariaDB, базы:
- `login_damoony` — для логина/сессий
- `www_damoony` — основная

Пользователь: `damoony` / `qwe12345` (пароль сменить).

### 11.6. DNS

- NS сейчас: `ns1.reg.ru`, `ns2.reg.ru`
- Cloudflare настроен, но NS не переключены (можно переключить позже — для Email Routing).
- DNS-записи править в панели reg.ru (пока NS там).

### 11.7. Почта

**Не настроена.** IP в Spamhaus PBL (residential, Эр-Телеком). Решение — внешний SMTP-релей.

---

## 12. Что НЕ трогать

- `www/.htaccess` — роутинг.
- `sources/config/` — настройки БД.
- `secure/main_settings.config` — не удалять, не переименовывать.
- `/etc/ssl/damoonyru/` — старый почтовый сертификат.
- `000-default.conf` — не включать обратно.
- `www2.zip` … `www6.zip` — бэкапы.

---

## 13. Порядок работы

1. **Прочитать этот манифест.**
2. **Проверить состояние:** `git log --oneline -10`, `git status --short`.
3. **Прогнать тесты** — убедиться, что всё зелёное.
4. **Задача приоритета 1** — начать с `render_display` в `main_settings`.
5. **После каждой задачи** — Restart OSPanel, тест, коммит.
6. **Следующая задача** — только после зелёного предыдущего этапа.

---

## 14. Открытые вопросы (для решения следующим агентом)

1. **`release_alternative` — строка или массив?**
2. **Удалять ли `final_ajax()`?** Или оставить алиасом.
3. **Кто ставит куку `js_enabled`?** `engine_ready.js`?
4. **`?_display=` — только URL или ещё профиль в БД?**
5. **Список ботов** для регулярки — расширять?
6. **Миграция слоёв на visuoel** — начинать с `bottom` или `center`?
7. **`visuostruct_view`** — создавать или отложить?

---

**Конец манифеста.**