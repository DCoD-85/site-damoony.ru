# Damoony2

Сайт на PHP 8.3+ с модульной архитектурой.
Домен: damoony2.ru
Разработка: собственный движок, версия alpha.0.0.

## Корень проекта

    secure\     — настройки сайта (main_settings.config)
    sources\    — движок сайта
    www\        — корень сайта (DOCUMENT_ROOT)
      ├── media\      — общедоступная статика (css, js, шрифты, иконки)
      ├── mediaall\   — публичные загрузки (изображения, аудио)
      └── logic\      — логика (закрыта .htaccess)
    _audit\     — документация и логи аудита
    _archive\   — старые версии файлов (не используются)
    _backup_*\  — слепки перед крупными изменениями

## Точка входа

    www\index.php

.htaccess переводит любой URL на index.php.

## Как это работает

1. index.php стартует сессию, определяет константы, подключает main.php.
2. main.php подключает sources\include.php (движок) и logic\alpha.0.0\include.php (логика).
3. sources\include.php разворачивает 18 модулей: render, user, db, core, library, visuo, gmod, mod и др.
4. logic\alpha.0.0\include.php разбирает URL:
   - находит домен в domain\,
   - находит аллиас в allias\,
   - запускает класс алгоритма через algoritm_.php.

## Модули

GLOBAL MOD (gmod): sources\gmod\*\*.php
MOD (mod):          sources\mod\*\*.php
Подключаются динамически по имени модуля.

## Логика аллиасов

Домен: www\logic\alpha.0.0\domain\<домен>\
Аллиас: www\logic\alpha.0.0\domain\<домен>\allias\<имя>\
Класс:  allias\<имя>\algoritm\algoritm.php + lang\<язык>\
Пример URL: https://login.damoony2.ru/en-en/form

## Подсистема visuo

Собственный UI-движок: дерево элементов.
Точка сборки: sources\visuo\visuo.php
Корень дерева: visuotag.
Релиз: visuorelease.

## Что делать при добавлении нового кода

1. PHP-класс — в sources\ или в logic\<версия>\.
2. Подключение — через include.php, не напрямую.
3. Имя файла — только латиница, без пробелов, без "копия".
4. Старая версия — в _archive\, не рядом с живой.
5. Крупное изменение — запись в _audit\cleanup-log.txt.

## Полезные файлы

    _audit\sitemap.md     — карта включений (кто кого подключает)
    _audit\conventions.md — правила именования
    _audit\cleanup-log.txt — история изменений

## История

- 2026-09-17 — аудит проекта. 471 рабочий файл, 21 файл в архиве.
  Убрано: 4 дубликата image*.png, пустой BMP, старые версии JS,
  устаревшие папки sources\other и sources\modules.
  dbcheck.php (552 KB) вынесен в архив как справочник БД.
