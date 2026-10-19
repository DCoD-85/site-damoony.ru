# App — Бизнес-логика

Здесь живут домены, аллиасы и страницы.

## 🧬 Контракт базового класса `algoritm_`
Все классы страниц наследуют `\algoritm_`. Основные методы:
- **`guest()`** — вызывается, если пользователь гость. Возвращает `true`, если рендер прошел успешно.
- **`logged()`** — вызывается для авторизованного пользователя.
- **`ajax_guest()` / `ajax_logged()`** — вызываются при AJAX-запросах (SPA-режим).
- **`$this->main`** — доступ к ядру (БД, настройки, `visuo`, `library`).
- **`$this->args`** — параметры из URL (например, `['lang' => 'ru-ru', 'login' => 'admin']`).

## 📄 Семантика `.data.json`
```json
{
    "default": {
        "available": [ "index", "docs" ],
        "auth": false,
        "search": true,
        "default": true
    }
}
```
- **`available`** — используется **только в `legacy`** движке (белый список URL-сегментов). В `alogic` эта настройка игнорируется, так как маршруты описаны в `routes.php`.
- **`auth`** — `true` (только для авторизованных, иначе вызов `algoritm_auth`), `false` (гостевой доступ).
- **`search`** — разрешать ли поиск по параметрам через GET (`?q=...`).
- **`default`** — страница по умолчанию для текущего уровня.

## Создание страницы (`/docs/api`)
1. Класс `algoritm_docs_api.php` в `algoritm/docs/api/`:
```php
namespace damoony_ru\visuo;
class algoritm_docs_api extends \algoritm_ {
    public function guest() { 
        $this->main->alliasglobal->center->content('API Docs');
        return true; 
    }
}
```
2. Маршрут в `routes.php` (аллиас `visuo`).