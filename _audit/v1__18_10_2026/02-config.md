# 02 — config/ + secure/ + language/

**Объект:** `config/` (3 файла), `secure/` (10 файлов), `language/` (4 файла)
**Дата:** 2026-10-18

---

## 1. Резюме

Конфигурация и секреты частично контролируются git, что создаёт риски утечки. `language/` не отдаётся через web, но использует `.inc`.

| Папка | Файлов | Оценка | Главный риск |
|---|---|---|---|
| `config/` | 3 | 5/10 | `site.config` (admin hash) в git |
| `secure/` | 10 | **3/10** | `db.secure` (credentials?) в git |
| `language/` | 4 | 7/10 | — |

---

## 2. config/

| Файл | В git | Назначение |
|---|---|---|
| `site.config` | ✅ | домены, аллиасы, fillers, admin.ips, logic |
| `render.config` | ✅ | display-режимы (dynamic/static) |
| `lang.config` | ✅ | дефолтный язык, список, путь |
| `site.config.lock` | ✅ | пустой, используется для flock |
| `site.config.bak` | 🚫 | резервная копия |

### G-1: `site.config` в git

**Проблема:** в `site.config` хранится `admin.hash` (пароль админки) и `admin.ips` (whitelist). Если хеш скомпрометирован через историю git — доступ восстановить нельзя, только менять.

**Фикс:**
1. Проверить наличие `admin.hash` (`grep -n '"admin"' config/site.config`).
2. Если есть — **сменить пароль админки** (через `corescripts admin:passwd`).
3. `git rm --cached config/site.config`.
4. Создать `config/site.config.example` (без hash, без ips).
5. Добавить в `.gitignore` (если ещё нет).

### `site.config.lock` в git

**Проблема:** пустой файл, нужен только для `flock()`. В git ему не место — при клонировании репозитория он создастся сам.

**Фикс:** `git rm --cached config/site.config.lock`, добавить в `.gitignore`.

---

## 3. secure/

| Файл | В git | Назначение |
|---|---|---|
| `db.secure` | ✅ | 164 байта — **что внутри?** |
| `db.config` | 🚫 | реестр БД (host, user, pass) |
| `admin_fails.json` | ✅ | runtime (счётчик неудачных логинов) |
| `blacklist/*` | ✅ | IP-фильтры (whitelist/blacklist) |
| `logs/admin.log` | 🚫 | audit log |

### G-2: `db.secure` в git

**Проблема:** 164 байта — это либо зашифрованные credentials, либо ключ. Если это credentials (пусть и base64) — они в git.

**Фикс:**
1. `cat secure/db.secure` — проверить содержимое.
2. Если credentials — **сменить пароли БД**, `git rm --cached secure/db.secure`, создать `secure/db.secure.example`.
3. Если это публичный ключ/соль — оставить, но задокументировать в `README`.

### G-3: `admin_fails.json` в git

**Проблема:** runtime-файл. Счётчик неудачных логинов не должен версионироваться (при деплое перезапишется).

**Фикс:** `git rm --cached secure/admin_fails.json`, добавить в `.gitignore`.

---

## 4. language/

| Файл | Формат | Назначение |
|---|---|---|
| `ru-ru.inc` | PHP | строки |
| `en-en.inc` | PHP | строки |
| `ch-ch.inc` | PHP | строки |
| `save.txt.inc` | PHP | сохранённые дефолты |

**Проблема:** `.inc`-файлы — если Apache отдаст их напрямую, содержимое утечёт (если там код без `<?php`). Сейчас есть `language/.htaccess` с запретом — **проверить**.

**Фикс:** убедиться, что `language/.htaccess` содержит `Deny from all` или `Require all denied`.

---

## 5. Рекомендации

**P0 (немедленно):**
1. Проверить `site.config` на `admin.hash` → сменить пароль → `git rm --cached`.
2. Проверить `db.secure` → если credentials → сменить пароли БД → `git rm --cached`.

**P1 (неделя):**
3. `git rm --cached` для `site.config.lock`, `admin_fails.json`.
4. Создать `.example`-файлы.
5. Проверить `language/.htaccess` на `Deny from all`.