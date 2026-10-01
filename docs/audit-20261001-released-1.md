# Пост-аудит реализации №1

**Дата:** 2026-10-01
**Основание:** `docs/audit-20261001.md`
**Статус:** выполнено

## Сводка

По итогам аудита от 2026-10-01 выполнены работы по перестройке структуры хранения публичной статики и приватных данных. Устаревшие папки удалены. Публичная папка `www/media/` пересоздана с разделением по аллиасам, скинам и типам ресурсов. Приватные данные выведены за пределы web-root в `data/`.

## 1. Удалено

| Папка | Расположение | Причина |
|---|---|---|
| `www/mediaall/` | web-root | Устаревшая, содержимое не использовалось |
| `www/media_old/` | web-root | Устаревшая, содержимое перенесено в новую структуру |

Подтверждено сканированием `Get-ChildItem www -Directory`: остались только `logic/`, `media/`, `tests/`.

## 2. Создано: `www/media/`

Публичная папка. Два уровня:

- `shared/` — кросс-домен, кросс-аллиас.
- `<allias>.<domain>/` — ресурсы конкретного аллиаса.

```
www/media/
├── shared/
│   ├── scripts/
│   │   ├── lib/                    vendor: jquery, plyr
│   │   └── engine/                 jsengine
│   ├── css/
│   └── images/
└── visuo.damoony.ru/
    ├── skin/
    │   ├── default/
    │   │   ├── css/
    │   │   ├── scripts/
    │   │   └── images/
    │   └── dark/
    │       ├── css/
    │       ├── scripts/
    │       └── images/
    ├── scripts/
    │   ├── lib/
    │   └── engine/
    ├── css/
    └── images/
```

## 3. Создано: `data/`

Приватная папка. Расположена вне web-root (рядом с `www/`). Прямой URL недоступен, отдача только через PHP-скрипт.

```
data/
├── users/
│   └── damoony.ru/
│       └── <user_id>/
│           ├── avatar/
│           ├── images/
│           └── files/
├── downloads/
│   └── damoony.ru/
│       └── visuo/
└── uploads/
    └── damoony.ru/
```

## 4. Принципы новой структуры

1. **`media/` — публично.** Статика: js, css, images.
2. **`data/` — приватно.** Пользовательские данные, курируемые файлы, загрузки.
3. **Уровни: `<allias>.<domain>` → скин → тип ресурса.** Один аллиас — одна папка верхнего уровня.
4. **`shared/` — кросс-домен, кросс-аллиас.** Общий js (vendor + jsengine), общий css, общие картинки.
5. **`skin/<name>/` содержит только специфичное для скина.** Общее для аллиаса — на уровень выше (`<allias>.css/`, `<allias>.images/`).
6. **`scripts/engine/` — наш код** (`engine.js`, `messages.js`, `engine_ready.js`, visuoel). **`scripts/lib/` — vendor** (jquery, plyr).
7. **`users/` вне web-root.** Приватность по умолчанию.
8. **`downloads/` и `uploads/` вне web-root.** Счётчик скачиваний и права доступа не обходятся прямым URL.
9. **`images/` — множественное число везде.**

## 5. Примеры URL и путей

**URL (публично):**

```
/media/shared/scripts/lib/jquery/1.8.3/jquery.min.js
/media/shared/scripts/engine/engine.js
/media/visuo.damoony.ru/skin/default/css/main.css
/media/visuo.damoony.ru/skin/default/scripts/start.js
/media/visuo.damoony.ru/css/dt.css
/media/visuo.damoony.ru/images/logo.svg
```

**Путь на диске (публично):**

```
/www/media/shared/scripts/engine/engine.js
/www/media/visuo.damoony.ru/skin/default/css/main.css
```

**Путь на диске (приватно):**

```
/data/users/damoony.ru/42/avatar/1.png
/data/downloads/damoony.ru/visuo/template-1.0.0.zip
/data/uploads/damoony.ru/42/document.pdf
```

## 6. Верификация

Выполнено:

- `tree /F /A www\media > media-tree.txt`
- `tree /F /A data > data-tree.txt`
- `Get-ChildItem -Path www\media -Recurse -File > media-files.txt`
- `Get-ChildItem -Path www -Directory` — подтверждено: `media_old` и `mediaall` отсутствуют.

## 7. Что осталось за рамками релиза №1

Не входит в объём выполненных работ и передаётся в следующие релизы:

- Правки PHP-кода под новые пути (`VISUO_MEDIA_DOWNLOADS`, `alliasscheme_*::meta()`, `sources/messages.php`, `www/index.php::error()`).
- Заглушки `index.php` и `.htaccess` в новых папках `media/**`.
- Шардинг `users/` по подпапкам.
- Мультидомен в URL.
- Единая точка входа для отдачи пользовательских данных (`/avatar/<id>.png`).
- Удаление `www/tests/`.