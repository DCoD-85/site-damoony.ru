# SimplCMS — документация

## Структура проекта
engine/ — ядро (main.php, constants.php, included.php)
logic/ — прикладная логика (домены, аллиасы)
sources/ — модули движка (db, library, visuo, ...)
corescripts/ — CLI + команды + библиотеки
config/ — JSON-конфиги
secure/ — секреты
language/ — строки локализации
_dev/ — инструменты (Composer, PHPStan, PHPUnit)
_audit/ — аудиты и roadmap
_doc/ — документация
www/ — публичная папка

## Публичный периметр

Только `www/` доступно по HTTP. Всё остальное — за пределами DocumentRoot.