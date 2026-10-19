# 01 — engine/

**Объект:** `engine/` (125 файлов)
**Дата:** 2026-10-18
**Оценка:** 5.6 / 10 (архитектура 8, безопасность 4)

---

## 1. Резюме

Ядро — сильнейшая часть проекта архитектурно, но содержит **P0-1** (plaintext пароли) и **4 новых P0** (middleware).

| Что проверено | Результат |
|---|---|
| `engine/main.php` self-reference | ✅ починен (`a6dcd16`) |
| `engine/include.php` switchboard | ✅ 12 модулей, `__DIR__` |
| `engine/user.php` | 🔴 **P0-1 активен** — plaintext |
| `engine/middleware.php` | 🔴 M-1…M-4 |
| `engine/visuo/` | ⚠️ 2 файла знают `main` |

---

## 2. Модули

| Файл | Роль | P0/P1 |
|---|---|---|
| `main.php` | класс main, main_path, main_settings | — |
| `include.php` | switchboard, 12 модулей | — |
| `constants.php` | DS/DSA + debug-флаги | — |
| `user.php` | аутентификация | **P0-1** |
| `db*.php` | слой БД | E-1 (`update`/`delete` `%s`) |
| `visuo/*` | HTML-билдер | P0-6 |
| `middleware.php` | security headers + rateLimit | **M-1…M-4** |
| `log.php`, `render.php`, `messages.php`, `mod.php`, `language.php`, `blacklist.php`, `vardump.php` | прочее | — |

---

## 3. P0-1 — engine/user.php (подтверждён)

**Факт:** `engine/main.php:73` → `$this->user = new user($this)`. **`engine/user.php` активен**, не legacy.

Найденные строки:

```text
engine/user.php:35   public string $password = '';
engine/user.php:50   'password' => $_COOKIE['password'] ?? ''
engine/user.php:59   SELECT * FROM toolsusers WHERE login=%s AND password=%s
engine/user.php:60   [ $cookies['login'], $cookies['password'] ]
engine/user.php:85   $hash = md5($login . $password . time());
engine/user.php:116  setcookie('login',    $login,    ...);
engine/user.php:117  setcookie('password', $password, ...);   ← plaintext
engine/user.php:118  setcookie('hash',     $hash,     ...);
```

**Что не так:**
- Пароль в cookie, SQL, БД — plaintext.
- `md5(...)` — не для паролей (для хэша cookie, но бесполезен).
- `visuo\user` уже на `password_hash` — но `engine/user.php` **всё ещё создаётся** в `main::init()`.

### Два пути

**A. Заменить `engine/user.php` на `visuo\user` в main**

```php
// engine/main.php:73
$this->user = new \damoony_ru\visuo\user($this);
```

- Плюс: 0 миграции, `visuo\user` уже написан.
- Минус: `engine/` начинает зависеть от `logic/` (нарушает слои).

**B. Переписать `engine/user.php` на `password_hash`**

- Добавить колонку `password_hash` в `toolsusers`.
- `authorize()`: `password_verify($password, $row['password_hash'])` — иначе fallback на legacy + lazy upgrade.
- Cookie: только `login` + opaque `session_token` (`random_bytes(32)`) в новой таблице `toolsusers_sessions`.

- Минус: ~4–6 часов + миграция БД.

**Решение:** **B**, но **сначала промежуточный фикс (30 минут)** — убрать `setcookie('password')` и добавить колонку. Полная миграция — при первом логине (lazy upgrade).

**Промежуточный фикс:**

```php
// engine/user.php:117 — удалить
setcookie('password', $password, ...);

// добавить:
$token = bin2hex(random_bytes(32));
setcookie('session_token', $token, ...);

// toolsusers: + password_hash VARCHAR(255)
// + toolsusers_sessions (token, user_id, expires)
```

---

## 4. M-1…M-4 — engine/middleware.php

Файл создан 17.10 (`6a2b922`). Содержит те же паттерны, что критиковались в audit-14.10.

### M-1 — Race в rateLimit

Текущий код:
```php
$raw = @file_get_contents($file);        // без лока
$parsed = @json_decode($raw, true);
...
@file_put_contents($file, json_encode($data), LOCK_EX);  // LOCK_EX только на write
```

**Симптом:** два параллельных запроса читают `count=9`, оба пишут `count=10`. Лимит 10/min фактически даёт 20/min. Brute-force закрыт **только на бумаге**.

**Фикс:**
```php
$fp = fopen($file, 'c+');
flock($fp, LOCK_EX);
$raw = stream_get_contents($fp);
$data = json_decode($raw, true) ?: ['count'=>0,'start'=>$now,'banned_until'=>0];
// ... логика ...
ftruncate($fp, 0); rewind($fp);
fwrite($fp, json_encode($data));
flock($fp, LOCK_UN); fclose($fp);
```

### M-2 — REMOTE_ADDR без trusted-proxy

Текущий код:
```php
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
```

**Симптом:** за Cloudflare/reverse-proxy все клиенты = IP прокси. 10 неудачных логинов одного атакующего банят всю прокси (DoS на легитимных).

**Фикс:** конфиг `trusted_proxies` в `site.config`; парсинг `X-Forwarded-For` **с конца** (последний — самый близкий к нам); fallback `REMOTE_ADDR`.

### M-3 — die вместо StopMiddleware

Текущий код:
```php
class StopMiddleware extends Exception {}
// ...
die( 'Too Many Requests' );   // ← не бросает, убивает pipeline
```

**Фикс:**
```php
throw new StopMiddleware();
```

И обработчик в `www/index.php` / `admin/bootstrap.php`:
```php
try { (new middleware())->run(); }
catch (StopMiddleware $e) { exit; }
```

### M-4 — CSP только в админке

`grep "Content-Security-Policy" engine/ www/` → только `admin/bootstrap.php:35`.

**Фикс:** добавить в `middleware::securityHeaders`:
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:");
```

`'unsafe-inline'` — потому что `onclick=` в `visuoattrsactions` (visuo S-1).

---

## 5. visuo — путь к независимости

**Проверено:** `grep "main->" engine/visuo/` → **2 файла**, знающих `main`:

| Файл | Строки | Что нужно |
|---|---|---|
| `visuopage.php` | 22, 23, 25 | `$main->visuorelease->visuotag->root()` |
| `visuorelease.php` | 87, 88 | `$main->visuopage->apply($struct)` |

`visuotag`, `visuoattrs*`, `visuoel*`, `visuostruct`, `visuolink`, `visuometa`, `visuolog` — **не знают `main`**. Уже независимы (доказано 431 тестом на Stubs).

### План выноса (1–2 дня)

1. **Интерфейс `VisuoHost`:**
   ```php
   interface VisuoHost {
       public function getRootTag(): visuotag;
       public function applyPageOverrides(visuostruct_html $struct): void;
   }
   ```
2. `visuopage` / `visuorelease` принимают `VisuoHost` в конструкторе.
3. `engine/main.php` реализует `VisuoHost` (2 метода-адаптера, ~20 строк).
4. `engine/visuo/` → `visuo/` (отдельная папка).
5. Тесты — моки `VisuoHost` вместо `Stubs.php`.

**Ценность:** переиспользование вне Damoony2, чёткая граница, тесты без фейк-`main`.

**Когда:** **после P0** (сейчас не трогаем).

---

## 6. Прочие находки engine/

| # | Файл | Проблема | Severity |
|---|---|---|---|
| E-1 | `db.php:update()` / `delete()` | авто-`%s` для int → coerce-исключения | P1 |
| E-2 | `db_mysqli_query::result()` | `fetch_all()` без `LIMIT` | P2 |
| E-3 | `db_schema::run()` | теряет `last_error` | P2 |
| E-4 | `library_link::ahref()` | `$k=$v&` без `urlencode` | P1 |
| E-5 | `messages::as_tag()` | новый `visuotag` на каждый рендер | P3 |
| E-6 | `language::savedefaults()` | I/O на каждом запросе | P2 |
| E-7 | `main_settings::loadExternalConfig()` | 4× `file_get_contents` без кэша | P2 |
| E-8 | `middleware::rateLimit` | `sys_get_temp_dir()` — без TTL-очистки | P3 |

---

## 7. Рекомендации по engine/

### P0 (сейчас)

1. `user.php` — убрать `setcookie('password')`, `password_hash`, opaque token.
2. `middleware.php` — flock, trusted-proxy, `StopMiddleware`, CSP.
3. `visuorelease.php:88` — `instanceof visuostruct_html`.

### P1 (неделя)

4. `db.php:update/delete` — авто-тип `%d` для int.
5. `library_link::ahref` — `http_build_query`.
6. `main::logic` — `class_exists` перед `new`.

### P2 (месяц)

7. `db_mysqli_query` — `$stmt->close()`, `$result->free()`.
8. Кэш JSON-конфигов (APCu).
9. `db_schema::run` — проверка `last_error`.

### P3

10. Вынос `visuo/` (1–2 дня).