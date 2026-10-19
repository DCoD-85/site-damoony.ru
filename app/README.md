# App — Бизнес-логика

Здесь живут домены, аллиасы и страницы. 

## 🗺 Соответствие namespace и путей
- Папка `domain/damoony.ru` -> namespace `damoony_ru`.
- Домен берется из `domains_namespace` в `site.config`.
- Аллиас `visuo` -> namespace `damoony_ru\visuo`.

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
- **`available`** — whitelist URL-сегментов, которые ведут на этот класс. Если сегмента нет в списке — 404.
- **`auth`** — `true` (только для авторизованных, иначе `algoritm_auth`), `false` (гостевой доступ).
- **`search`** — разрешать ли поиск по параметрам.
- **`default`** — является ли страница дефолтной для текущего уровня.

## Создание страницы (`/docs/api`)
1. **Класс** `algoritm_docs_api.php` в `algoritm/docs/api/`:
```php
namespace damoony_ru\visuo;
class algoritm_docs_api extends \algoritm_ {
    public function guest() { return true; }
}
```
2. **Данные** `algoritm_docs_api.data.json` (см. выше).
3. **Маршрут** в `routes.php` (аллиас `visuo`).