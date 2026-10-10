================================================================
SimplCMS — corescripts
================================================================

Инструменты разработчика для управления доменами, аллиасами,
блоками, страницами. Не для публичного доступа.

ИСПОЛЬЗОВАНИЕ
----------------------------------------------------------------
  php corescripts/cli.php <command> [args]

КОМАНДЫ
----------------------------------------------------------------
  domain:new <domain> [--register]
      Создаёт www/logic/alpha.0.0/domain/<domain>/
      со всем набором: allias.php, alliasglobal.php, alliasscheme.php,
      dataprovider.php, alliasglobal/ (7 блоков), alliasscheme/ (3 схемы),
      allias/www/ (минимальный аллиас).

      --register — также записать домен в config/site.config
                   (domains_real, domains_available, domains_namespace,
                    alliases_real, alliases_available, fillers).

  allias:new <name>       (в разработке)
  help                    Показать справку

ПРИМЕРЫ
----------------------------------------------------------------
  # создать домен без регистрации (для теста)
  php corescripts/cli.php domain:new test.ru

  # создать домен и сразу зарегистрировать в site.config
  php corescripts/cli.php domain:new idoam.ru --register

  # создать с переопределённым namespace
  php corescripts/cli.php domain:new example.com --namespace=example_com

СТРУКТУРА
----------------------------------------------------------------
  cli.php                 точка входа (парсер аргументов)
  _lib/                   вспомогательные классы
    Cli.php               вывод, ask/confirm
    Fs.php                mkdir/write/rmrf, rel-путь
    Template.php          рендер .tpl, scan, lint
    Config.php            site.config (get/set/save с бэкапом)
    Validate.php          валидация доменов/имён/namespace
    Result.php            структурированный результат
    Context.php           прогресс-коллбэк (для CLI/web)
    Renderer.php          рендер результата (CLI)
  commands/               логика команд (без UI)
    DomainNew.php         plan() + run() + register()
  templates/
    domain/               53 .tpl-файла — эталон домена
      <domain_ns>         подставляется namespace (test.ru → test_ru)
      <domain>            подставляется домен (test.ru)

БУДУЩЕЕ (в разработке)
----------------------------------------------------------------
  domain:list             список домен
  domain:remove           удалить домен
  allias:new              создать аллиас
  allias:remove           удалить аллиас
  page:new                создать страницу в аллиасе
  block:new               создать глобальный блок
  filler:new              создать filler блока

  Веб-админка (позже)     UI поверх тех же commands/*.php