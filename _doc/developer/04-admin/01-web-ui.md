# 💻 Управление: Веб-интерфейс (Админка)

Админка SimplCMS полностью изолирована от публичной части сайта (фронтенда). Она не загружает `visuo` или `engine/main.php`, чтобы обеспечить безопасность и скорость работы.

**Путь:** `www/admin/`

## Архитектура (MVC)

Начиная с версии `alpha.0.0`, админка переведена на классическую MVC-архитектуру. Гигантский `switch` в `bootstrap.php` был удален.

1. **Точка входа (`index.php` / `bootstrap.php`)** — проверяет авторизацию (`Auth`), CSRF-токены (`Csrf`), обрабатывает инвайты и fingerprint-автовход.
2. **Роутер (`_lib/AdminRouter.php`)** — принимает параметр `?p=...`, разбивает его на `ресурс:действие` (например, `domain:edit`), находит нужный контроллер и вызывает его метод.
3. **Контроллеры (`controllers/`)** — вся бизнес-логика. Например, `DomainController.php`, `ResourceController.php`. Они наследуются от `BaseController`.
4. **Модель (`corescripts/`)** — контроллеры не пишут в БД или файлы напрямую, а вызывают классы из `corescripts/` (например, `Config::mutate()`, `DataGateway::insert()`).
5. **Представление (`views/`)** — HTML-шаблоны. Они подключаются через класс `View::render()`.

## Как работает Роутер (AdminRouter)

Роутер имеет внутреннюю карту маршрутов (массив `$map`):
    'dashboard' => 'DashboardController',
    'domain'    => 'DomainController',
    'resource'  => 'ResourceController',

Если пользователь заходит по адресу `?p=resource:form`, роутер:
1. Понимает, что ресурс — это `resource`.
2. Ищет в карте `ResourceController`.
3. Ищет в классе метод `form()`.
4. Вызывает его.

Если контроллер или метод не найдены, роутер возвращает `false`, и `bootstrap.php` отдает страницу 404.

## Рендеринг страниц (View)

Каждая страница админки имеет свой файл в папке `views/`. 
В контроллере это выглядит так:

    $this->render( 'dashboard' , [
        'domains' => $rows,
        'meta' => [
            'title' => 'Домены',
            'actions' => '<a href="?p=domain:new" class="btn-primary">+ Добавить домен</a>'
        ]
    ] );

### Мета-данные ($meta)
Вместо того чтобы хардкодить заголовки и кнопки в каждом view, используется массив `$meta`. 
Главный шаблон `_layout.php` автоматически отрисовывает:
*   `title` — заголовок страницы (H1).
*   `breadcrumbs` — хлебные крошки (массив `[['label' => '...', 'url' => '...'], ...]`).
*   `actions` — HTML-код кнопок в правом верхнем углу.
*   `tabs` — вкладки (Домен, Аллиас).

## Навигация (Вкладки)

В SimplCMS используется двухуровневая навигация:
1. **Вкладки домена** (`views/_domain_tabs.php`) — Настройки, Аллиасы, Блоки, Схемы, Медиа.
2. **Вкладки аллиаса** (`views/_allias_tabs.php`) — Настройки, Страницы, Филлеры, Схема, Данные (БД).

Они управляются через `$meta['tabs']`:

    'tabs' => [
        'show' => true, 
        'domain' => true, 
        'allias' => true, 
        'active_domain' => 'allias', 
        'active_allias' => 'pages'
    ]

## Как создать новую страницу в админке

1. **Добавьте контроллер:** Создайте файл в `www/admin/controllers/MyController.php`. Унаследуйте его от `BaseController`.

       class MyController extends BaseController {
           public function index(): void {
               $this->render('my_view', [
                   'data' => ['foo' => 'bar'],
                   'meta' => ['title' => 'Моя страница']
               ]);
           }
       }

2. **Добавьте маршрут:** Откройте `www/admin/_lib/AdminRouter.php` и добавьте ресурс в массив `$map`.

       private static array $map = [
           // ...
           'my_page' => 'MyController'
       ];

3. **Создайте вьюху:** Создайте файл `www/admin/views/my_view.php`.

       <div class="table">
           <p>Значение: <?= htmlspecialchars( $data['foo'] ) ?></p>
       </div>

4. **Проверьте в браузере:** Перейдите по адресу `?p=my_page`.

## Безопасность

*   Все POST-запросы (кроме логина) требуют CSRF-токен: `<?= Csrf::field() ?>`.
*   Доступ к `bootstrap.php` защищен проверкой сессии (`$auth->check()`).
*   Файлы конфигурации защищены от прямого доступа через Nginx/Apache, админка работает с ними только через PHP.