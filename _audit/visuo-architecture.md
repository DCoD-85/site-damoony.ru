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

---

## СХЕМЫ АЛЛИАСОВ — полный обзор

Обновлено: 2026-09-18.

Все схемы лежат в `www\logic\alpha.0.0\domain\<домен>\alliasscheme\`.
Namespace всех — `damoony` (весь домен = один пул).

### Общий контракт схемы

Каждая схема — класс с одинаковым набором методов:

    __construct( alliasscheme )
        получает $main, $visuorelease

    meta( )
        очищает visuorelease->meta() и наполняет CSS/JS

    body_preload( )
        возвращает visuotag с preload/load-слоями

    body( )
        очищает visuorelease->visuotag, строит HTML-скелет, вызывает visuotag_set()

    clear( )
        сбрасывает 7 ссылок на слои (dt_*)

    init( )
        находит слои в свежем дереве через find(class)

    refresh( bool $reload )
        заполняет слои содержимым из alliasglobal

    load( )
        meta → body → init → refresh

    reload( )
        meta->clear → clear → meta → body → init → refresh(true)

### Общие поля

    public null|\main         $main
    public null|alliasscheme  $alliasscheme
    public null|\visuorelease $visuorelease

    public null|\visuotag $dt_root
    public null|\visuotag $dt_top
    public null|\visuotag $dt_middle
    public null|\visuotag $dt_left
    public null|\visuotag $dt_center
    public null|\visuotag $dt_right
    public null|\visuotag $dt_bottom

### 4 схемы

#### alliasscheme_login — полная, для аллиаса login

Скелет:

    !doctype → html.scheme_login → head → body
        body_preload( )
        div#popup_result
        div.dt
            div.dt_top
            div.dt_middle
                div.dt_center
                div.dt_right
            div.dt_bottom
        div.dt_debug

Слои: dt_top, dt_center, dt_right, dt_bottom (без left).
Meta: полный набор CSS/JS + plyr (CDN).

#### alliasscheme_error — сокращённая, для ошибок

Скелет:

    !doctype → html.scheme_error.<class>.<guest|logged> → head → body
        body_preload( )
        div#popup_result
        div.dt
            div.dt_top
            div.dt_middle
                div.dt_center
            div.dt_bottom
        div.dt_debug

Слои: dt_top, dt_center, dt_bottom (без left/right).
Класс html = `scheme_error` + get_class($this) + guest/logged.
Meta: CSS с `error.css` вместо `center.css`, без plyr.

#### alliasscheme_min — минимальная, для AJAX

Скелет:

    div.dt_center

Слои: только dt_center.
Meta: пустая (`$meta->clear()`).
Используется в `algoritm_lang_login::default_guest()` при `$_POST`,
перед `visuorelease->release_min( $struct )`.

**Это не HTML-схема.** Это заглушка под JSON-ответ:
`release_min` собирает JSON, а не HTML.

#### alliasscheme_www — полная, для главной

Скелет:

    !doctype → html.cl_html.<class>.<guest|logged> → head → body
        body_preload( )
        div#popup_result
        div.dt
            div.dt_top
            div.dt_middle
                div.dt_left
                div.dt_center
                div.dt_right
            div.dt_bottom
        div.dt_debug

Слои: dt_top, dt_left, dt_center, dt_right, dt_bottom — полная сетка.
Класс html = `cl_html` + get_class($this) + guest/logged.
Meta: полный набор + разные CSS для guest/logged (`guest/dt.css`, `logged/dt.css`).

### Сравнительная таблица

| Схема | dt_top | dt_left | dt_center | dt_right | dt_bottom | meta | Использование |
|---|---|---|---|---|---|---|---|
| login | ✓ | — | ✓ | ✓ | ✓ | полный + plyr | форма логина |
| error | ✓ | — | ✓ | — | ✓ | с error.css | ошибки |
| min | — | — | ✓ | — | — | пусто | JSON-ответ |
| www | ✓ | ✓ | ✓ | ✓ | ✓ | полный + guest/logged | главная |

### Что общего и что разное

**Общее (одинаково во всех):**

- namespace `damoony`
- поля main/alliascheme/visuorelease
- поля dt_root/dt_top/dt_middle/dt_left/dt_center/dt_right/dt_bottom
- методы meta / body_preload / body / clear / init / refresh / load / reload
- `body_preload()` — базовая разметка preload + load
- `body()` всегда вызывает `visuorelease->visuotag_clear()` в начале
- `body()` всегда заканчивается `visuorelease->visuotag_set( $vt )`
- `init()` всегда находит слои через `$vt->root()->find( class : 'dt_...' )`
- `refresh()` всегда наполняет слои через `alliasglobal->{top,left,center,right,bottom}->as_tag()`
- `reload()` — перерисовка без перезагрузки страницы

**Разное:**

- скелет HTML в `body()` (какие слои и в каком порядке)
- класс на `<html>` (scheme_login / scheme_error / cl_html + guest/logged)
- наполнение meta (какие CSS/JS подключаются)
- `body_preload()` может быть с popupload (login, www) или без (error, min)
- `body()` в min — не строит `!doctype / html / head / body`, только div.dt_center

### Точка входа

Из алгоритма логики:

    $scheme = $main->alliasscheme;
    $scheme->load( 'login' );    // или 'error', 'min', 'www'

`alliasscheme::load( $name )`:

    $this->schemename = $schemename;
    $this->{$schemename}->load( );

`alliasscheme` держит 4 публичных свойства-схемы:

    public null|alliasscheme_login  $login  = null;
    public null|alliasscheme_error  $error  = null;
    public null|alliasscheme_min    $min    = null;
    public null|alliasscheme_www    $www    = null;

Создаются в `alliasscheme::init()`, который вызывается из `autoload()`.

### Расширение — как добавить новую схему

1. Создать файл `logic\alpha.0.0\domain\<домен>\alliasscheme\alliasscheme_<имя>.php`.
2. namespace `damoony`.
3. Класс `alliasscheme_<имя>`, повторяет контракт (см. выше).
4. В `alliasscheme.php`:
   - добавить `include_once` в `autoload()`
   - добавить публичное свойство `$<имя>`
   - добавить `new alliasscheme_<имя>($this)` в `init()`
5. Вызывать `$main->alliasscheme->load( '<имя>' )` из алгоритма.

### Замечание

Схемы внутри одного домена имеют **дубликаты кода**:

- `meta()` почти одинаков у login/www/error (отличия только в 1–3 строках)
- `body_preload()` одинаков у login/www/error
- `init()`, `refresh()`, `clear()`, `reload()`, `load()` — идентичны во всех четырёх
- `body()` различается только скелетом

**Это осознанное решение.** Каждая схема — самодостаточный файл, независимо правится под нужды своего аллиаса. Общая база не вводится, потому что цена ошибки в базе — падение всех схем сразу.

Возможный рефакторинг (не сейчас) — вынести общий скелет в базовый класс `alliasscheme_base`, оставить наследникам только `body()`. Но это **отдельный спринт**.
