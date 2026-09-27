# Damoony2 — карта проекта

Обновлено: 2026-09-17 (после Sprint 5).

## Точка входа

    www\index.php  →  www\main.php  →  sources\include.php  →  модули
                                    →  www\logic\alpha.0.0\include.php  →  логика

.htaccess настроен: любой URL → www\index.php.

## Граф включений (живое)

    www\index.php
    ├── www\constants.php
    ├── www\included.php
    └── www\main.php
        ├── sources\include.php
        │   ├── sources\render.php
        │   ├── sources\user.php
        │   ├── sources\blacklist.php
        │   ├── sources\vardump.php
        │   ├── sources\log.php
        │   ├── sources\core.php
        │   │   ├── sources\core\core__path.php
        │   │   ├── sources\core\core__settings.php
        │   │   ├── sources\core\var.php
        │   │   └── sources\core\generate.php
        │   │       └── sources\core\generate\generate_css.php
        │   ├── sources\config.php
        │   ├── sources\db.php            → dynamic: db_mysqli.php, db_mysqli_query.php
        │   ├── sources\library.php
        │   │   ├── gen.php, dir.php, url.php, link.php,
        │   │   │   vars.php, file.php, image.php
        │   ├── sources\system.php
        │   ├── sources\language.php      → dynamic: language\load.php, save.php
        │   ├── sources\messages.php
        │   ├── sources\visuo.php
        │   │   └── sources\visuo\visuo.php
        │   │       ├── visuotag.php (корень дерева) — подсистема visuo
        │   │       ├── visuorelease.php + visuorelease_scheme.php
        │   │       ├── visuoelement.php, visuoelements.php (28 подэлементов)
        │   │       ├── visuostruct.php → visuostruct_min.php, visuostruct_ajax.php
        │   │       ├── visuoattrs(.php + 3 подфайла), visuometa.php, visuodata.php
        │   │       └── visuolog.php, visuolist.php, visuolink.php, visuoscript.php
        │   ├── sources\gmod.php          → dynamic: sources\gmod\*\*.php (GLOBAL MOD)
        │   └── sources\mod.php           → dynamic: sources\mod\*\*.php  (MOD)
        └── www\logic\alpha.0.0\include.php
            ├── domain.php
            │   ├── allias.php
            │   ├── alliaslocal.php        → top.php, bottom.php
            │   ├── alliasglobal.php       → auth, headers, scheme, menu,
            │   │                            top, left, center, right, bottom
            │   └── alliasscheme.php       → alliasscheme_{login,error,min,www}.php
            ├── domainglobal.php           → header.php
            ├── algoritmdata_.php
            └── algoritm_.php              → dynamic: $path → allias алгоритмы

## Логика аллиасов

Домен: `damoony.ru` (реальный, если аллиасов несколько).

Структура аллиаса (`allias\<имя>\`):

    include.php               → algoritm.php + subalgoritm.php
    algoritm\algoritm.php     — базовый класс
    algoritm\algoritm_auth.php  — авторизация
    algoritm\algoritm_error.php — ошибка
    algoritm\lang\...         — языковые обработчики
    subalgoritm\...           — top, left, right, bottom, topmenu

Пример URL: `https://login.damoony2.ru/en-en/form`
  домен: damoony2.ru
  аллиас: login
  класс:  algoritm_lang_form

## Что в _archive\ (не используется, но сохранено)

| Файл в архиве | Что это |
|---|---|
| sources-other\ | старый debug/includer/loader (закомментирован в include.php) |
| sources-modules\ | устаревший дубль gmod/mod |
| dbcheck-old-db-version.php | референс старой БД (ранее 552 KB) |
| db_mysqli-class1.php | другая версия класса БД |
| visuopage-class22.php | другая версия visuopage |
| fb-2023.js | старая версия fb.js |
| js-old\ | старые JS (formblock, shablonizator) |

## Особые подсистемы

### visuo (UI собственной разработки)
Дерево элементов: `visuotag` — объект динамического дерева, `visuorelease` — релиз.

### gmod / mod
- `gmod.php` — global mod (класс `gmod`, префикс `gmod_`)
- `mod.php`  — mod (класс `mod`, префикс `mod_`)
Оба подключаются из `sources\include.php:28-29`.

### dbcheck
Класс `class__dbcheck` — устаревший скрипт создания/восстановления БД.
Оставлен в архиве как справочник по структуре БД.
