# 💻 Тестирование: PHPUnit и CI/CD

Визуальный раннер (`www/tests/`) отлично подходит для проверки UI-компонентов `visuo`. 
Однако для строгого тестирования бизнес-логики (например, классов `Paths`, `tree.php`, `Config`) используется стандартный **PHPUnit** и статический анализатор **PHPStan**.

**Путь к тестам:** `_dev/`

## Структура папки `_dev/`

*   `composer.json` — конфигурация зависимостей (PHPUnit ^10.5, PHPStan ^1.11).
*   `phpunit.xml` — конфигурация тестового окружения.
*   `phpunit/` — папка с самими тестами (классы, наследуемые от `TestCase`).
*   `vendor/` — установленные пакеты (создается автоматически).

## Установка

Если вы только клонировали репозиторий, установите зависимости:

```bash
cd _dev
composer install
```

## Запуск тестов

Все команды выполняются из папки `_dev/`:

**Запустить все тесты:**
```bash
composer test
```

**Запустить тесты с проверкой типов (PHPStan):**
```bash
composer check
```

**Запустить конкретный файл:**
```bash
vendor/bin/phpunit phpunit/TreeTest.php
```

## Как написать тест для PHPUnit

Тесты хранятся в `_dev/phpunit/` и наследуются от `PHPUnit\Framework\TestCase`.

Пример теста для проверки роутера (`_dev/phpunit/TreeMatchTest.php`):

```php
<?PHP

use PHPUnit\Framework\TestCase;

class TreeMatchTest extends TestCase {

    private function invokeMatch( string $segment, array $available ) : array {
        $tree = new \tree( new \main() );
        $method = new ReflectionMethod( \tree::class , 'match' );
        $method->setAccessible( true );
        return $method->invoke( $tree , $segment , $available );
    }

    public function test_empty_segment_matches_default() : void {
        [ $target , $arg ] = $this->invokeMatch( '' , [ '' ] );
        
        $this->assertSame( '' , $target );
        $this->assertNull( $arg );
    }

    public function test_regex_with_param() : void {
        [ $target , $arg ] = $this->invokeMatch( 'my-post' , [ '[a-z0-9-]+|slug' ] );
        
        $this->assertSame( 'slug' , $target );
        $this->assertSame( 'slug' , $arg );
    }
}
```

## Статический анализ (PHPStan)

PHPStan проверяет код на наличие логических ошибок и несоответствий типов без его запуска.

```bash
composer stan
```

Конфигурация находится в `_dev/phpstan.neon`. По умолчанию установлен уровень 6. 
Если анализатор находит ошибку, он выдает номер строки и файл. Это помогает поддерживать код в чистоте.

## Настройка CI/CD (GitHub Actions)

Для автоматического запуска тестов при каждом пуше в GitHub, создайте файл `.github/workflows/tests.yml` в корне проекта:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, json
          tools: composer

      - name: Install dependencies
        run: |
          cd _dev
          composer install --prefer-dist --no-progress

      - name: Run PHPStan
        run: |
          cd _dev
          composer stan

      - name: Run PHPUnit
        run: |
          cd _dev
          composer test
```

Теперь при каждом коммите GitHub Actions будет прогонять ваши тесты и проверять типы. Если что-то сломается — вы получите уведомление.