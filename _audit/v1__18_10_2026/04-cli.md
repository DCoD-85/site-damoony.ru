# 04 — corescripts/ + _dev/

**Объект:** `corescripts/` (13 команд + _lib), `_dev/` (инструменты)
**Дата:** 2026-10-18
**Оценка:** corescripts 6.4 / 10, _dev 7.5 / 10

---

## 1. Резюме

CLI — самый зрелый слой после engine. Но есть P0-3 и P0-4 (транзакционность удаления), а также race conditions.

---

## 2. P0-3, P0-4: удаление без отката

**Файлы:** `corescripts/commands/DomainRemove.php:42`, `AlliasRemove.php:55`

```php
Fs::rmrf($domain_path);      // ← файлы удалены
$cfg->removeKeys([...]);     // ← конфиг обновляется после
$cfg->save();
```

**Проблема:** если `$cfg->save()` упадёт (нет прав, диск), файлы удалены, а конфиг указывает на несуществующее.

**Фикс:** двухфазный commit.
1. Записать конфиг в `.tmp`.
2. Переместить файлы в `trash/` (не удалять).
3. Атомарно переименовать `.tmp` → `config`.
4. По успеху — `rmrf(trash)`.
5. По ошибке — откатить.

---

## 3. _dev/bin

Phar'ы (`composer`, `phpstan`, `phpunit`) перенесены в `_dev/bin/` (коммит `3fa382f`). Это правильно — корень `_dev/` чистый.

---

## 4. P1: дублирование

- `private static function render()` в 5 командах (`DomainNew`, `AlliasNew`, `PageNew`, `BlockNew`, `FillerNew`).
- `get_domain_ns()` в 4 командах.
- `preg_replace_callback` для табов в 3 местах.

**Фикс:** вынести в `Template::render()`, `Config::getDomainNs()`, `Fs::tabsJsonEncode()`.

---

## 5. P2: Lint в web-контексте

`Template::lint()` вызывает `exec('php -l')` для каждого файла. В CLI — секунды, в админке — таймаут.

**Фикс:** флаг `--no-lint` для web-контекста.

---

## 6. Рекомендации

**P0:**
1. `DomainRemove` / `AlliasRemove` — транзакционность.

**P1:**
2. Вынести `render()`, `get_domain_ns()` в общие хелперы.

**P2:**
3. `--no-lint` для web.