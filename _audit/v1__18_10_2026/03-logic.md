# 03 — logic/

**Объект:** `logic/alpha.0.0/` (роутинг, алгоритмы, схемы)
**Дата:** 2026-10-18
**Оценка:** 5.1 / 10 (архитектура 7, безопасность 5)

---

## 1. Резюме

Самый сложный и запутанный слой. Хорошая идея (роутинг через алгоритмы, filler'ы, схемы), но реализация страдает от критических багов и копипасты.

| Проблема | Severity |
|---|---|
| CORS через Referer | **P0-2** |
| `menu::text()` сигнатуры | **P0-5** |
| `include_ext` default + `$base[1]` | P1 |
| `class_exists` перед `new` | P1 |
| 3 копии схем (min/www/visuo) | P2 |

---

## 2. P0-2: CORS через Referer

**Файл:** `logic/.../allias/visuo/algoritm/algoritm.php:54-62`

```php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$access_origin = '*';
if ($referer !== '') {
    $access_origin = substr($referer, 0, strlen($referer) - 1);
}
header('Access-Control-Allow-Origin: ' . $access_origin);
header('Access-Control-Allow-Credentials: true');
```

**Проблема:**
- `substr(..., -1)` — обрезка последнего символа бессмысленна и опасна.
- `Allow-Credentials: true` + произвольный origin = любой сайт делает авторизованные запросы.
- `HTTP_REFERER` подделывается.

**Фикс:** удалить блок целиком (родительский `algoritm_::logic()` уже умеет CORS правильно) или использовать `HTTP_ORIGIN` + whitelist по `domains_available`.

---

## 3. P0-5: menu::text() сигнатуры

**Файлы:** `logic/.../alliasglobal/menu.php:55` vs `menu_row.php:40`

```php
// menu.php
public function text(string $name, string $text, bool $active = false): alliasglobal_menu_row

// menu_row.php
public function text(string $text): static
```

**Проблема:** если кто-то вызовет `$menu->text('a', 'b', 'c')` — PHP кинет `ArgumentCountError`. Если `$menu_row->text('a', 'b')` — тоже.

**Фикс:** выровнять сигнатуры. Логичнее, чтобы `menu.php` возвращал `menu_row`, а текст устанавливался через `menu_row->text($text)`.

---

## 4. P1: Роутинг

### include_ext: default-ветка

`algoritm_.php:220`:
```php
mb_ereg('^' . $base[0] . '$', $url_base_real[$url_base_pos], $matches);
```
`$base[1]` — это первая группа regex, а не именованная. Если в `available` лежит `lang`, то `$base[1]` вернёт всю скобку.

**Фикс:** использовать `preg_match` с именованными группами или переписать логику.

### include_ext: class_exists

`algoritm_.php:246`:
```php
$this->algoritm_classes[$url_base_pos] = new $class($main, $args);
```

**Проблема:** если класса нет — fatal error. Нужно `if (!class_exists($class)) { http_response_code(404); return; }`.

---

## 5. P2: Дублирование схем

`alliasscheme_min.php`, `alliasscheme_www.php`, `alliasscheme_visuo.php` — почти идентичны (отличаются только CSS/JS и слотами). ~300 строк × 3.

**Фикс:** `alliasscheme_base` с параметрами (slots, meta), от него наследуются остальные.

---

## 6. Рекомендации

**P0:**
1. `algoritm.php:54-62` — убрать CORS-логику.
2. `menu.php` / `menu_row.php` — выровнять сигнатуры `text()`.

**P1:**
3. `algoritm_.php:220` — именованные группы.
4. `algoritm_.php:246` — `class_exists` перед `new`.

**P2:**
5. `alliasscheme_base` для трёх схем.