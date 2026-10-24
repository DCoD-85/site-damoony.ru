_dev — изолированный dev-стек проекта

НАЗНАЧЕНИЕ
Все инструменты разработки, тесты и служебные скрипты живут здесь.
Прод-код — в ../engine/, ../corescripts/, ../www/.
_dev/ в web-доступе отсутствует (вне DocumentRoot).

СТРУКТУРА
_dev/
  README.md              этот файл
  composer.json          зависимости, autoload, скрипты
  phpunit.xml            конфиг PHPUnit 10
  phpstan.neon           конфиг PHPStan
  phpstan-baseline.neon  baseline
  bootstrap.php          общие константы и helpers для тестов
  stubs.php              заглушки для классов ядра
  bin/                   локальные phar-бинарники
    composer.phar
    phpstan.phar
    phpunit.phar
    smoke.sh             smoke-проверка прод-URL (cron)
  scripts/               служебные CLI-скрипты
    test_admin.php       smoke ядра админки
  phpunit/               legacy PHPUnit-тесты (classmap)
  tests/                 новые тесты (PSR-4, Damoony\Tests)
  vendor/                composer deps (gitignore)

УСТАНОВКА
cd _dev
php bin/composer.phar install

ЗАПУСК ТЕСТОВ
Все сьюты:
cd _dev
php bin/phpunit.phar -c phpunit.xml

Только legacy:
php bin/phpunit.phar -c phpunit.xml --testsuite legacy

Только new:
php bin/phpunit.phar -c phpunit.xml --testsuite new

СТАТИЧЕСКИЙ АНАЛИЗ
cd _dev
php bin/phpstan.phar --configuration phpstan.neon --no-progress

SMOKE ПРОД-URL
_dev/bin/smoke.sh
Пишет лог в /mnt/data/logs/smoke_YYYYMMDD.log.
При ошибке — в /mnt/data/logs/alerts.log.

SMOKE ЯДРА АДМИНКИ
php _dev/scripts/test_admin.php

СОГЛАШЕНИЯ
Новые тесты — только в _dev/tests/<подраздел>/, namespace Damoony\Tests\...
Legacy-тесты в _dev/phpunit/ не переносить без причины.
Секреты и креды — только в secure/ и config/*.config, они в .gitignore.
Никаких require_once в новом коде — только composer autoload.
