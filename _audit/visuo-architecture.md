# Visuo — архитектура подсистемы

Обновлено: 2026-09-17 (Sprint 8).
Статус: живая подсистема. 23 живых файла, ~95 KB.

## Что это

Visuo — собственный построитель HTML-дерева на PHP.
Не шаблонизатор, а объектная модель DOM:

  - visuotag        — узел дерева (как DOM-элемент)
  - visuoattrs      — атрибуты (id, class, style, actions)
  - visuodata       — data-* атрибуты
  - visuometa       — meta-теги (link, script)
  - visuoelement    — обёртка элемента (надстройка над тегом)
  - visuoelements   — конкретные элементы (form, input, table, block, link)
  - visuorelease    — рендер дерева в HTML
  - visuorelease_scheme — скелет документа (html/head/body)

## Точка сборки

  sources\visuo.php  →  sources\visuo\visuo.php
                        ↑ подключает всё живое

## Граф

  sources\visuo\visuo.php
  ├── visuorelease_settings.php
  ├── visuolog.php
  ├── visuolist.php
  ├── visuolink.php
  ├── visuoscript.php
  ├── visuotag.php                    ← ЯДРО
  │   ├── visuoattrs.php
  │   │   ├── attrs/class.php
  │   │   ├── attrs/style.php
  │   │   └── attrs/actions.php
  │   ├── visuodata.php
  │   ├── visuometa.php
  │   └── visuostruct.php
  │       ├── visuostruct_min.php
  │       └── visuostruct_ajax.php
  ├── visuoelement.php                ← ОБЁРТКА ЭЛЕМЕНТА
  │   └── visuoelements.php           ← загрузчик 26 элементов
  │       ├── form/ + form/form.php
  │       ├── input/ + 14 типов
  │       ├── table/ + tablebox, block, blocktext
  │       ├── block/ + text, block
  │       └── link/ + simple, load, popup
  └── visuorelease.php                ← РЕНДЕР
      └── visuorelease_scheme.php     ← СКЕЛЕТ

## Ключевые классы — методы

### visuotag (ядро)
Создание:   tag(), structv(), element()
Атрибуты:   attrs_add(), attrs_class_add(), attrs_style_add(), attrs_actions_add()
Данные:     data_add()
Навигация:  parent(), find(), findstruct(), next()
Вставка:    insert(), prepend(), append(), before(), after()
Рендер:     release(), prepare(), releasetree()
Шорткаты:   root(), html(), head(), title(), body(), div(), span()

### visuoelement (обёртка)
Хранит: visuoelement__data, visuoelement_group, visuotag_start, visuotag_end.
Group — конкретная реализация (form|input|table|block|link|user).

### visuorelease (рендер)
Два корня: visuotag (рабочее) + visuotag_final (финал).
release() — обход дерева и вывод HTML.
releasetree() — для дебага.

### visuorelease_scheme (скелет)
Задаёт структуру документа: html > head + body.
Содержит пресеты: preset_vt_default, preset_vt_error, preset_vm_default, preset_vm_error.
Пресеты сейчас отключены (закомментированы в preset_init).

## Эволюция (что было раньше)

Visuo пережил три волны рефакторинга. В _archive\visuo-obsolete\ лежат версии:

  visuoclass.php          → предок visuoattrs\class
  visuostyle.php          → предок visuoattrs\style
  visuoactions.php        → предок visuoattrs\actions
  visuoscheme.php         → предок visuorelease_scheme
  visuoscheme_release.php → промежуточная версия схемы
  visuopage.php           → другая концепция (страница через класс) — отказ
  visuodata_addclass.php  → отключён в visuo.php
  visuoelements\user.php  → сломанный (принимает form в конструктор)
  visuoelements\text\link22.php → дубль класса text\link.php
  visuoelements\block\block2.php → вариант с опечаткой bock
  visuoelements\form\_form.php   → старая версия form.php

## Что важно помнить при работе с visuo

1. Visuo не подключается напрямую — только через sources\visuo.php.
2. Новые элементы добавлять в visuoelements\ и регистрировать в visuoelements.php.
3. visuotag::tag() возвращает новый тег; чтобы вернуться к родителю — return:false.
4. visuorelease требует main — иначе render_classic = true по умолчанию.
5. Схема документа: release("default") — стандартный скелет.

## Правила именования для visuo (из conventions.md)

1. Имя класса = visuo + тип + __data для данных.
2. Файл = имя класса без visuoelement_ префикса.
3. Версия файла → в _archive, не рядом с живым.

---

## СХЕМА ДОКУМЕНТА — где она на самом деле

Обновлено: 2026-09-18.
Статус: схема живёт в logic. В движке — устаревший рудимент.

### Рантайм-факт

    www\main.php:69
    new visuorelease( $this , scheme_use : false );

`scheme_use = false` передаётся ВСЕГДА. Значит:

    visuorelease::init( )
        → schemenone_init( )
            создаёт пустой скелет: !doctype → html → head → body

Схема `visuorelease_scheme` в рантайме НЕ используется.

### Где живёт настоящая схема

    www\logic\alpha.0.0\domain\<домен>\
    ├── alliasscheme.php                    ← менеджер: load / reload
    └── alliasscheme\
        ├── alliasscheme_login.php          схема для аллиаса login
        ├── alliasscheme_min.php            схема для min-режима (AJAX)
        ├── alliasscheme_error.php          схема ошибок
        └── alliasscheme_www.php            схема главной

Namespace: `damoony` (весь домен = один пул).

### Как управляется схема

Из алгоритмов логики:

    $scheme = $main->alliasscheme;      // damoony\alliasscheme
    $scheme->load( 'min' );             // в algoritm_lang_login
    $scheme->load( 'login' );           // в algoritm_lang_form (см. примеры)
    $scheme->load( 'www' );             // в www-алгоритме

`load()` у alliasscheme:

    public function load ( string $schemename ) {
        $this->schemename = $schemename;
        $this->{$schemename}->load( );
    }

Вызывает одноимённый метод у объекта схемы (`login`, `min`, `error`, `www`).
Внутри `alliasscheme_login::load()`:

    public function load ( ) {
        $this->meta( );      // наполняет visuorelease->meta() ссылками CSS/JS
        $this->body( );      // строит HTML-скелет и вызывает visuorelease->visuotag_set()
        $this->init( );      // находит слои dt_top/dt_left/dt_center/dt_right/dt_bottom
        $this->refresh( );   // заполняет слои через alliasglobal->{top,left,center,right,bottom}
    }

### Что делает каждая схема

| Схема | Роль |
|---|---|
| `alliasscheme_login` | Полный HTML: `!doctype`, `html.scheme_login`, `head`, `body`, `dt_top`, `dt_middle`, `dt_center`, `dt_right`, `dt_bottom`, `dt_debug`, preload, popup-слои. Полный CSS/JS в meta. |
| `alliasscheme_min` | Минимальный: только `div.dt_center`. Meta очищена. Для AJAX/JSON-ответов. |
| `alliasscheme_error` | Схема ошибки — пока не изучена. |
| `alliasscheme_www` | Схема главной — пока не изучена. |

### Трёхфазная модель и схема

Схема работает в фазах 1 и 2:

    1. main.php → new visuorelease( scheme_use: false )
       создан пустой !doctype → html → head → body

    2. Логика аллиаса строит содержимое
       $scheme->load( 'login' )
         → схема ПЕРЕЗАПИСЫВАЕТ дерево через visuorelease->visuotag_clear() + visuotag_set()
         → строит свой скелет с слоями dt_*

    3. Логика наполняет слои
       $center->visuotag( )->append( $vt_center )

    4. visuorelease->final( )
       ├── find('head')->meta( $this->visuometa )   ← мета в head
       ├── visuotag->prepare( )                    ← init() всех visuoelement
       ├── visuotag->release( )                    ← HTML
       └── visuotag_final->final( )                ← строка

### Что в движке — мертво

`sources\visuo\sources\visuorelease_scheme.php`:

- **Не используется в рантайме.** Все упоминания — в самом движке (`visuo.php`, `visuorelease.php`).
- **Класс `visuorelease_scheme_default::release()` возвращает `$this->vt`, которого не существует** → если кто-то включит схему, всё упадёт.
- **Ветки `scheme_use === true`** в `visuorelease.php` (строки 43, 66, 100-102) — недостижимы. `scheme_use` всегда `false`.
- **Старые классы `visuorelease_scheme_error` и `visuorelease_scheme_error2`** — закомментированы целиком.
- **`preset_init()`** — весь код внутри `/* */`, метод только `print`-ит debug.

**Кандидат на вынос в `_archive\visuo-obsolete\`.** Но НЕ сейчас. Сначала — стабильность.

### Что важно помнить

1. **Схема — часть проекта, а не движка.** Это архитектурное решение.
2. **У каждого проекта — своя схема.** В `logic\<версия>\domain\<домен>\alliasscheme\`.
3. **Движок даёт механизмы.** `visuotag`, `visuoelement`, `visuostruct`, `visuometa`.
4. **Схема собирает документ.** Она знает про слои, CSS, JS, meta.
5. **Управление — через `$main->alliasscheme->load( '<имя>' )`** из алгоритма.
6. **`visuorelease_scheme` в движке — не использовать.** Мёртвый рудимент другой архитектуры.
