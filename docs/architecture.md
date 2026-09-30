# Архитектура

## Слои

Проект разделён на четыре слоя. Смешивать нельзя.

    UI (алгоритмы страниц)
    www/logic/.../allias/*/algoritm/lang/*
             │
             │ $main->dataprovider->current->…
             ▼
    Dataprovider (именованные запросы)
    allias/*/dataprovider/*.php
             │
             │ $main->db->query(…)
             ▼
    Слой БД (mysqli-драйвер)
    sources/db.php + sources/db/mysqli/*
             │
             │ mysqli
             ▼
    MariaDB

### Правила

**UI.** Можно: HTML, `visuotag`, `visuoel`, данные из dataprovider. Нельзя: SQL, `mysqli`, работа с БД напрямую.

**Dataprovider.** Можно: `$main->db->query()`. Нельзя: HTML, бизнес-логика.

**Слой БД.** Можно: драйвер-специфика. Нельзя: знания о таблицах проекта.

**MariaDB.** — .

### Смена СУБД

Правится только `sources/db/mysqli/` → `sources/db/pgsql/` плюс `db_type` в `secure/db.secure`. Dataprovider и UI не меняются.

## Роутинг

URL имеет вид:

    https://visuo.damoony.ru/ru-ru/docs/installation/
                              └─┬─┘ └─┬─┘ └────┬────┘
                              lang  раздел   страница

**Обработка:**

1. `algoritm.php` — аллиас `visuo`, обрабатывает `([a-z]{2}-[a-z]{2})|lang`.
2. `algoritm_lang.php` — распознаёт `docs`, `news`, `login` и т.д.
3. `algoritm_lang_docs.php` — список или `([a-z0-9-]+)|page`.
4. `algoritm_lang_docs_page.php` — конкретная статья.

**Соглашение.** Каждый аллиас имеет `algoritm/lang/` — уровень языка. Внутри — папки-разделы, в каждой — файл `algoritm_lang_{section}.php`.

## Аллиас

**Аллиас** — это модуль проекта. У каждого:

- своя БД
- свои сессии (cookie + таблица)
- свой `algoritm` (роутер)
- свой dataprovider
- свой `include.php`

**Текущие аллиасы:**

- `www` — основной сайт
- `login` — авторизация
- `visuo` — UI-движок (этот)

**Общие для всех:** `alliasglobal/` (блоки, меню, скины) и `alliasscheme/` (шаблоны страниц).

## Блоки alliasglobal

Каждая страница рендерится через слоты:

- `dt_top` — шапка (логотип, языки, login)
- `dt_left` — левое меню
- `dt_center` — основной контент (заполняется алгоритмом)
- `dt_right` — контекстное меню
- `dt_bottom` — подвал

**Блок** (`alliasblock`) — класс, который:

1. Ищет `{block}_{allias}.php` и создаёт `alliasglobal_{block}_{allias}`.
2. Наполняет `menu` через `data()`.
3. Пришивает HTML через `release()`.

Пример: `alliasglobal/left/left_visuo.php` — меню для visuo.

## Dataprovider

Фасад `allias/{name}/dataprovider/dataprovider.php` собирает подклассы (`user`, `doc`, `news`, `files`, `stats`, `audit`) и отдаёт их через `$main->dataprovider->current`.

**Соглашение.** Методы возвращают `array`: одна строка — `->row() ?: []`, много — `->result()`.

## UI-движок visuo

- `visuotag` — HTML-строитель (низкий уровень)
- `visuoel` — компоненты (form, field, table, message, tabs, block)

**Правило.** В алгоритмах использовать `visuoel`. `visuotag` — только для специфичных случаев, где компонентов не хватает.

## Namespace

- Ядро (`main`, `db`, `language`, `visuotag`) — глобальный namespace
- Аллиасы — `damoony\{allias}`
- Блоки — `damoony` (без аллиаса)