# 🤖 PROJECT CONTEXT: SimplCMS (Damoony)

Этот файл — «шпаргалка» для разработчика и ИИ-ассистента. Загрузи его в начале общения, чтобы сразу погрузиться в контекст.

## 1. О проекте
SimplCMS — высокопроизводительная гибридная CMS для управления мультидоменной сетью сайтов. 
Ключевая архитектура: Data (JSON) -> Tree Router (Файловый роутер) -> Logic (PHP + visuo).
Вместо тяжелой базы данных для структуры используются атомарные JSON/PHP файлы. 
Движок использует **единый файловый роутер (tree)**. Логика страниц строится по физическому дереву URL: `/algoritm/p1/p2.php`.

## 2. Окружение и Стек
- Локальный ПК: Windows (OSPanel), GitBash.
- Сервер: Debian 13 (production).
- PHP: 8.4.25 (строгая типизация, неймспейсы, современные фичи).
- БД: MariaDB (для динамического контента).
- Корень проекта: E:\OSPanel543\domains\damoony2.ru\
- Прямой доступ к PHP 8.4.25: E:\OSPanel543\modules\php\PHP_8.4

## 3. Железные правила (Правила взаимодействия)
1. Git: Любое изменение файлов должно заканчиваться git add -A и коммитом (стиль: feat:, fix:, refactor:).
2. Команды: Все команды даются ТОЛЬКО для GitBash (с путями вида /e/OSPanel543/...).
3. Краткость: Отвечать коротко, без воды. Не писать лишних объяснений, если не просят.
4. Синтаксис: В PHP использовать только <?PHP (верхний регистр).
5. Безопасность: Никаких eval(). Использовать Fs::writeAtomic(), Json::writeAtomic(), Config::mutate().
6. Пути: Запрещены хардкоды путей. Использовать только класс Paths::.
7. Рендеринг: HTML на фронтенде генерируется только через visuotag и visuoel. В админке допускается чистый HTML во вьюхах.
8. БД: Прямые SQL-запросы запрещены. Использовать только DataGateway::.

## 4. Дерево структуры проекта (Ключевые узлы)
- /app/alpha.0.0/domain/ - Данные доменов (PHP-классы страниц, .rules.json)
  - /allias/{alias}/resources/ - ★ Файлы *.resource.php (описание таблиц для CRUD)
- /config/ - Конфиги
  - engine.config (настройки движка: logic, ui_ajax_forms)
  - site.config (структура: домены, аллиасы, филлеры)
  - site.stubs.config (библиотека заглушек)
  - admin.security.config (настройки безопасности: whitelist, fingerprint)
- /corescripts/ - Ядро управления
  - /commands/ - Логика CLI-команд
  - /_lib/ - Классы Fs, Json, Paths, Config, Scan, DataGateway, ResourceManager
  - cli.php - CLI точка входа
- /engine/ - Ядро движка
  - main.php (оркестратор)
  - tree.php (★ файловый роутер)
  - algoritm_.php (базовый класс страниц)
- /visuo/ - UI-движок (visuotag, visuoel, components)
- /www/admin/ - Изолированная админка
  - /controllers/ - ★ MVC-контроллеры (Domain, Page, Resource и т.д.)
  - /views/ - HTML-шаблоны
  - /_lib/AdminRouter.php - Роутер админки
- /_dev/ - Инструменты разработчика (phpstan, phpunit)
- /_doc/ - Документация проекта

## 5. Статус и что сделано (История)
- **Безопасность**: Внедрены fingerprint-авторизация, инвайты, режимы whitelist/blacklist, защита от сброса своих устройств.
- **MVC Админка**: Удален старый switch в bootstrap.php. Роутинг переведен на AdminRouter и контроллеры (BaseController).
- **Resource-Driven CRUD**: Создан механизм для авто-генерации CRUD в админке. Файлы *.resource.php описывают структуру таблиц. ResourceManager сам создает/обновляет таблицы в БД (CREATE/ALTER). DataGateway выполняет запросы.
- **Связи (Relations)**: В CRUD добавлена поддержка Foreign Keys (выпадающие списки).
- **Тесты**: 469 зеленых тестов (PHPUnit) + PHPStan (level 5) чистый.

## 6. Roadmap (Что дальше)
Приоритеты: 1) Создание сайта документации `visuo.damoony.ru` через новый CRUD, 2) CLI-команда `db:migrate`, 3) UI-Kit для visuo.