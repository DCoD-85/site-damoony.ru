# Разработка

## Правила

1. Работаем только локально. Боевой — только `git pull`.
2. Одна основная ветка — `main`.
3. Коммит = одна фича. Не смешивать удаление мусора и новую фичу.
4. Коммит-сообщение — что сделано, а не «fix» или «update».
5. Перед правкой — `git pull`.
6. Перед коммитом — `git status`, проверить, что добавляешь.
7. Не коммитить: `secure/db.config`, `.env`, `cache/`, логи, `.zip`-архивы > 5 МБ.

## Git

    # Статус
    git status -s

    # Добавить конкретные файлы (не git add .)
    git add path/to/file.php

    # Коммит
    git commit -m "allias/visuo: fix login redirect"

    # Push
    git push origin main

    # Sync
    git pull origin main

**Префиксы коммитов:**

- `allias/visuo:` — изменения в аллиасе visuo
- `alliasglobal:` — блоки, меню, схемы
- `sources/db:` — слой БД
- `docs:` — документация
- `cleanup:` — удаление мусора
- `fix:` — исправление бага
- `chore:` — рутина (обновление, конфиги)

## Line endings

Все файлы — LF. Windows должен работать с LF. Файл `.gitattributes` в корне гарантирует, что git автоматически конвертирует.

Если видишь `warning: CRLF will be replaced by LF` — это норма.

Для текстовых редакторов:

- VS Code: `files.eol: "\n"`
- Notepad++: `Edit → EOL Conversion → Unix (LF)`

## Namespace и стиль

**Namespace:**

- Ядро — глобальный (`main`, `db`, `language`)
- Аллиас — `damoony\{name}` (`damoony\visuo`)
- Блоки — `damoony` (без аллиаса)

**Стиль:**

- Отступы — табы
- Открывающая скобка функции — на той же строке через пробел: `function foo ( ) {`
- Пробелы внутри скобок: `foo( $a , $b )`
- Пробелы вокруг операторов: `$a = $b + $c ;`

## Dataprovider

**Новый метод** — сначала в dataprovider, потом вызывать в алгоритме.

    // dataprovider_news.php
    public function recent ( int $limit = 10 , string $lang = 'ru-ru' ) : array {
        $limit = max( 1 , min( 100 , $limit ) );
        return $this->query(
            'SELECT ... FROM news WHERE status = %s AND lang = %s ORDER BY published_at DESC LIMIT %d' ,
            [ 'published' , $lang , $limit ]
        )->result( );
    }

**Правила:**

- SQL только в dataprovider
- Placeholder `%s` / `%d` — не конкатенация
- Валидация входных (`max`, `min`, `in_array`)
- Возврат: `->row()` для одной (с `?: []`), `->result()` для многих

## Алгоритм страницы

    <?PHP
    namespace damoony\visuo;

    class algoritm_lang_news extends \algoritm_ {
        public array $available = [ '' , '([a-z0-9-]+)|page' ];
        public bool  $auth      = false;

        public function release ( ) {
            $this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
            $this->release_( $this );
        }

        public function guest  ( ) { return $this->render( ); }
        public function logged ( ) { return $this->render( ); }

        private function render ( ) {
            // ...
            return true;
        }
    }

## UI (visuoel)

**Формы:**

    $ve = new \visuoel( );
    $form = $ve->form( )->form( );
    $form->data( )->set( 'name' , 'post' , '' );

    $csrf = new \visuoel_input_hidden( );
    $csrf->data( )->set( '_csrf' , csrf_token( ) );
    $form->add( $csrf );

    $f = $ve->field( )->text( );
    $f->data( )->set( 'login' )->label( 'Логин' )->required( );
    $form->add( $f );

    $main->alliasglobal->center->visuotag( )->el( $ve );

**Нет `field()->submit()`.** Используй `new \visuoel_field_submit()` напрямую.

**Таблицы:**

    $table = $ve->table( );
    $row = $table->row( );
    $row->cell( 30 )->set( 'Ключ' );
    $row->cell( 70 )->set( 'Значение' );

## Отладка

**Логи:**

    tail -f /var/log/apache2/damoony.ru-error.log
    tail -f /var/www/damoony.ru/www/error.log

**Тестовые страницы** (`www/tests/`) — только для локальной разработки. Не деплоить.

**OPcache.** После правки класса — Restart OSPanel (или `systemctl reload php8.4-fpm`).

## Что делать при добавлении аллиаса

1. Создать папку `allias/{name}/`.
2. `include.php` — каркас.
3. `algoritm/algoritm.php` — роутер.
4. `algoritm/lang/` — страницы.
5. `dataprovider/` — фасад плюс подклассы.
6. Добавить в `config/site.config` → `alliases_available`.
7. Прописать в `secure/db.config` БД.
8. Создать БД плюс миграции.
9. Добавить в `alliasglobal` — `left_{name}.php`, `right_{name}.php`.
10. Тестировать локально.

## Чего избегать

- `git add .` — только конкретные файлы
- `git push --force` — никогда
- SQL в алгоритмах
- `htmlspecialchars` вручную — используй `h()`
- `echo` — используй `visuotag`
- Правки на боевом
- Коммиты с сообщениями типа `fix` / `update` / `temp`

## Нюансы MariaDB на Windows

### Хост подключения

Всегда указывать `-h 127.0.0.1`, не `localhost`.

**Почему:** на Windows `localhost` может резолвиться в IPv6 `::1`, а MariaDB слушает только IPv4 `127.0.0.1`. Результат — `ERROR 2002: Can't connect to server`.

### SSL

Клиент MariaDB 11.8 требует SSL по умолчанию. Но сервер OSPanel стартует с `skip-ssl`. Результат — `ERROR 2026: TLS/SSL error: SSL is required, but the server does not support it`.

**Решение:** флаг `--skip-ssl` в каждой команде клиента:

    mysql.exe --skip-ssl -h 127.0.0.1 -u root -pqwe12345

### Аутентификация

MariaDB 11.8 может использовать `auth_gssapi_client` для root — PHP mysqli не поддерживает.

**Проверка плагина:**

    SELECT user, host, plugin FROM mysql.user WHERE user IN ('root','damoony');

**Решение:** переключить на `mysql_native_password`:

    ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('qwe12345');
    ALTER USER 'damoony'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('qwe12345');
    FLUSH PRIVILEGES;

**Важно:** `CREATE USER`, `ALTER USER`, `GRANT` не работают в режиме `--skip-grant-tables` (ошибка `ERROR 1290`). Сначала стартовать сервер нормально.

### Инициализация (создание БД с нуля)

MariaDB, в отличие от MySQL, **не имеет** `mysqld --initialize-insecure`. Использовать:

    mariadb-install-db.exe --datadir="путь" --password="пароль"