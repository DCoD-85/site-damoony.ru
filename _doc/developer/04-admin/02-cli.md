# 💻 Управление: CLI-команды

В SimplCMS есть возможность управлять структурой сайта без использования браузера. Это полезно для скриптов миграции, массового создания страниц или работы на сервере без графического интерфейса.

**Точка входа:** `corescripts/cli.php`

## Базовый синтаксис

Все команды запускаются из корня проекта в терминале:

```bash
php corescripts/cli.php <имя_команды> [параметры]
```

Параметры передаются в формате `--ключ=значение` или просто `--флаг`.

## Список доступных команд

Вызовите `php corescripts/cli.php help`, чтобы увидеть полный список, или используйте следующие основные команды:

### 1. Создание домена
```bash
php corescripts/cli.php domain:new damoony2.ru --register
```
* `--register` — добавит домен в `site.config` автоматически.

### 2. Создание аллиаса
```bash
php corescripts/cli.php allias:new www --domain=damoony.ru --register
```
* Создаст структуру папок аллиаса и сгенерирует базовые филлеры для всех блоков.

### 3. Создание страницы
```bash
php corescripts/cli.php page:new about --domain=damoony.ru --allias=www --parent=lang
```
* `--parent` указывает путь родительской папки, где нужно создать страницу.

### 4. Извлечение данных из старых страниц (VarsExtract)
Если вы обновляете проект со старой версии CMS, используйте эту команду для генерации файлов `.rules.json` на основе старых `.data.json`:
```bash
php corescripts/cli.php vars:extract --domain=damoony.ru --allias=www
```

### 5. Изменение настроек
```bash
php corescripts/cli.php settings:set logic=alpha.0.0 --connect_type=https://
```
* Запишет изменения в `engine.config`.

### 6. Смена пароля администратора
```bash
php corescripts/cli.php admin:passwd admin --password=NewSecretPass
```

## Как это работает изнутри

Каждая команда — это отдельный PHP-класс в папке `corescripts/commands/`. 
Класс должен содержать статический метод `run(array $params, ?Context $ctx): Result`.

1. **`$params`** — массив аргументов, переданных из консоли.
2. **`$ctx`** — контекст для вывода сообщений (например, `$ctx->step('Валидация', true)`).
3. **`Result`** — объект, содержащий статус выполнения (успех/ошибка), логи и предупреждения.

### Пример простейшей команды

Если вы хотите добавить свою команду, создайте файл в `corescripts/commands/MyCommand.php` и добавьте его в маппинг `cli.php`.

```php
<?PHP

class MyCommand {
    public static function run( array $params , ?Context $ctx = null ) : Result {
        $r = new Result();
        
        $name = $params['name'] ?? 'default';
        
        // Логика команды
        $ctx?->step( 'Выполняю команду для ' . $name , true );
        $r->step( 'Команда выполнена' , true );
        
        return $r;
    }
}
```