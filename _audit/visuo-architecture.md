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
