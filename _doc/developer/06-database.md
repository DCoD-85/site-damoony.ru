# 💻 База данных: Работа с Dataprovider

В SimplCMS структура сайта (URL, страницы, правила) хранится в JSON-файлах. Однако для динамического контента (статьи, пользователи, товары, настройки) используется база данных **MariaDB**.

Чтобы код был чистым и безопасным, прямые SQL-запросы (`$pdo->query(...)`) в логике страниц **запрещены**. Вся работа с БД инкапсулирована в классе `dataprovider`.

## 1. Архитектура Dataprovider

Каждый домен имеет свой фасад для работы с БД: 
`app/.../domain/damoony.ru/dataprovider.php`

Внутри он наследует базовый класс (`dataprovider_base.php`), который содержит методы подключения и выполнения запросов. 

При загрузке страницы, `main.php` создает объект `$this->main->dataprovider`, через который вы получаете доступ ко всем таблицам этого домена.

## 2. Базовые методы

Базовый класс `dataprovider_base` (обычно) предоставляет следующие методы для безопасной работы с PDO:

*   `query( string $sql, array $params = [] )` — выполняет SELECT-запрос и возвращает массив строк. Использует подготовленные выражения (защита от SQL-инъекций).
*   `row( string $sql, array $params = [] )` — возвращает одну строку.
*   `val( string $sql, array $params = [] )` — возвращает одно значение (например, COUNT).
*   `insert( string $table, array $data )` — вставляет запись.
*   `update( string $table, array $data, string $where, array $params )` — обновляет записи.

*(Если в вашей реализации методы называются иначе, ориентируйтесь на текущий код `dataprovider_base.php`)*.

## 3. Пример: Вывод списка статей

В классе страницы (`algoritm_lang_blog.php`) вы обращаетесь к провайдеру:

```php
public function default() {
    // Получаем последние 5 статей
    $posts = $this->main->dataprovider->news->get_latest( 5 );
    
    // Создаем таблицу через Visuo
    $table = (new \visuoel())->table()->table();
    $table->html_mode(true);
    
    $head = $table->head()->row();
    $head->cell()->set('Дата');
    $head->cell()->set('Заголовок');
    
    $body = $table->body();
    foreach ( $posts as $post ) {
        $row = $body->row();
        $row->cell()->set( $post['date'] );
        $row->cell()->set( $post['title'] );
    }
    
    $vt = new \visuotag();
    $vt->el( $table );
    $this->main->alliasglobal->center->section_add('blog_list', $vt);
    
    return true;
}
```

## 4. Создание собственных методов (Модель)

Логику конкретных таблиц лучше выносить в отдельные классы внутри папки `dataprovider/` аллиаса.

**Пример:** `app/.../allias/visuo/dataprovider/dataprovider_news.php`

```php
<?PHP
namespace damoony_ru\www;

class dataprovider_news extends \dataprovider_base {
    
    public function get_latest( int $limit = 10 ) : array {
        $sql = "SELECT * FROM news ORDER BY created_at DESC LIMIT :limit";
        // Важно: используем биндинг для параметров
        return $this->query( $sql , [ 'limit' => $limit ] );
    }

    public function create( string $title, string $text ) : int {
        return $this->insert( 'news' , [
            'title'      => $title,
            'text'       => $text,
            'created_at' => date('Y-m-d H:i:s')
        ] );
    }
}
```

Затем в главном `dataprovider.php` домена вы регистрируете это свойство:

```php
class dataprovider extends \dataprovider_base {
    public dataprovider_news $news;
    
    public function __construct( \main $main ) {
        parent::__construct( $main );
        $this->news = new dataprovider_news( $main );
    }
}
```

## 5. Миграции

Для создания структуры таблиц (SQL-схемы) в SimplCMS используются миграции. 
Они хранятся в виде SQL-файлов в папке `allias/visuo/migrations/`.

Пример файла `001_create_news.sql`:
```sql
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Для применения миграций используется CLI-команда или специальный скрипт-аппликатор, который выполняет эти SQL-файлы по очереди.