Задача: полный аудит кода и структуры проекта.

Цель
Составить полную карту проекта: что где лежит, кто кого подключает, что используется, что мёртво, где узкие места. Результат — docs/audit-YYYYMMDD.md.

Не менять код. Только читать и документировать.

Область аудита
Весь проект: sources/, www/logic/, config/, secure/. Все аллиасы: www, login, visuo. Общие: alliasglobal, alliasscheme.

Структура итогового документа
1. Карта модулей ядра
Для каждого файла в sources/*.php:

Назначение (одной строкой)

Что подключает (зависимости)

Кто его подключает

Статус: используется / не используется / дубль

Список файлов для разбора:

text
sources/db.php
sources/language.php
sources/library.php + sources/library/*
sources/core.php + sources/core/*
sources/system.php
sources/render.php
sources/user.php
sources/messages.php
sources/vardump.php
sources/blacklist.php
sources/config.php + sources/config/*
sources/gmod.php + sources/gmod/*
sources/mod.php + sources/mod/*
sources/visuo/* (visuo.php, visuotag.php, visuoel/*, visuostruct/*, visuorelease.php)
2. Карта аллиасов
Аллиас visuo (www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/):

include.php — что подключает

algoritm/* — какие страницы

dataprovider/* — какие методы

helpers.php, user.php — что используется

migrations/* — какие миграции

Аллиас www (allias/www/):

То же. Отметить, что можно удалить или объединить.

Аллиас login (allias/login/):

То же.

3. Карта alliasglobal и alliasscheme
Блоки: top, left, right, center, bottom, policy.
Схемы: www, error, login, min, visuo.
Скрипты: head, headers.

Для каждого: что делает, кто вызывает, есть ли мёртвый код.

4. Карта роутинга
Как URL превращается в класс:

algoritm_.php — база

allias/*/algoritm/algoritm.php — роутер аллиаса

algoritm_lang.php — уровень языка

algoritm_lang_{section}.php — страница

Составить таблицу всех маршрутов: URL → класс → файл.

5. Циклические зависимости
Найти include_once цепочки, которые могут зацикливаться. Проверить:

Кто подключает main.php

Как allias/visuo/include.php попадает в работу

Нет ли дублей include_once

Инструмент: grep -rn "include_once\|require_once" в sources/ и www/logic/.

6. Мёртвый код
PHP:

Методы, которые нигде не вызываются (grep по имени)

Классы без подключений

Неиспользуемые properties

Закомментированный код старше 1 месяца

CSS/JS:

Файлы в www/media/release/default/, которые ни на что не ссылаются

Селекторы CSS, которые не используются в HTML

Изображения:

Файлы в www/media/img/, не упомянутые в коде

Инструменты:

bash
# Для каждого класса/метода:
grep -rn "ClassName" sources/ www/ --include="*.php" | wc -l
# Если 1 — только объявление. Если >1 — используется.
7. Производительность
N+1 запросы:

bash
grep -rn "foreach.*query\|foreach.*->by_\|foreach.*articles_by\|foreach.*->all_" \
  www/logic/ sources/ --include="*.php"
Найти:

Циклы, которые выполняют запросы

SELECT * без LIMIT

Запросы в foreach

Проверить:

alliasglobal/left/left_visuo.php — цикл по разделам, вызывает articles_by_section

alliasglobal/right/right_visuo.php — то же

dataprovider_* — индексы в БД

Индексы БД:

bash
mysql -u root -e "SHOW INDEX FROM visuo_damoony.users; SHOW INDEX FROM visuo_damoony.news; ..."
Сверить с типичными запросами в dataprovider.

8. Безопасность
Проверить:

Все ли POST-формы имеют CSRF (csrf_check)

Все ли SELECT/INSERT/UPDATE через placeholder (%s, %d)

Экранируется ли пользовательский вывод через h()

Защита от path traversal в download

Правильные Content-Type при отдаче файлов

Хранение паролей через password_hash

Роли — где проверяются (can())

SQL-инъекции в динамических запросах (dataprovider_news::create, update)

Инструменты:

bash
# Формы без CSRF:
grep -rn "<form" www/logic/ | grep -v "csrf"
# SQL без placeholder:
grep -rn "SELECT.*\$\|INSERT.*\$\|UPDATE.*\$" www/logic/ sources/
9. Схема БД
Все таблицы во всех БД (visuo_damoony, login_damoony, www_damoony):

Назначение

Ключи, индексы, связи

Что не используется (пустые таблицы с нулём запросов)

Что дублируется

10. Итоговая сводка
Таблица оценок:

Архитектура ядра

Аллиас visuo

Dataprovider

UI-движок

Безопасность

Производительность

Мёртвый код (в %)

Документация

Список рекомендаций по рефакторингу:

Что чистить (мёртвый код, дубли, лишние файлы)

Что улучшить (N+1, безопасность, индексы)

Что задокументировать

Что НЕ трогать (работающее)

Формат итогового файла
docs/audit-YYYYMMDD.md:

markdown
# Аудит проекта damoony.ru

**Дата:** YYYY-MM-DD
**Автор:** агент
**Область:** весь проект

## Краткое резюме

Оценка общего состояния. Что критично, что нормально, что хорошее.

## 1. Ядро (sources/*)

## 2. Аллиасы (www, login, visuo)

## 3. Alliasglobal / Alliasscheme

## 4. Роутинг

## 5. Циклические зависимости

## 6. Мёртвый код

## 7. Производительность

## 8. Безопасность

## 9. Схема БД

## 10. Рекомендации

### Что чистить
### Что улучшить
### Что документировать
### Что не трогать
Дополнительно
Если найдёшь критический баг — не исправлять, только описать и создать issue.

Если код неясен — оставить пометку // TODO(audit): unclear в тексте отчёта.

Использовать цифры: сколько файлов проанализировано, сколько мёртвого кода, сколько N+1.

Приёмка
□ Отчёт создан, лежит в docs/audit-YYYYMMDD.md
□ Пройдены все 10 разделов
□ Дана оценка по 8 параметрам
□ Рекомендации сгруппированы (чистить / улучшить / документировать / не трогать)
□ Коммит и push в GitHub