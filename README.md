# Damoony2 — Lightweight Multi-Domain PHP Framework

**Damoony2** — легковесный мультидоменный микро-фреймворк с собственной маршрутизацией и независимым HTML-билдером. Работает на **PHP >= 8.4**.

## 🏗 Архитектура (4 слоя)
1. **`engine/`** — Ядро (`main`, `db`, `user`, `middleware`).
2. **`engines/`** — Сменные движки маршрутизации (Strategy): `legacy` (магия файлов) и `alogic` (явные маршруты).
3. **`app/`** — Бизнес-логика (домены, аллиасы, страницы).
4. **`visuo/`** — Независимый HTML-билдер (через `VisuoHost`).

## 🌐 3 состояния (режима) работы
1. **SSR (No JS)** — Полная перезагрузка (SEO-friendly).
2. **With JS** — JS перехватывает формы, страница рендерится целиком.
3. **SPA (AJAX)** — Подгрузка блоков (`dt_center`) без перезагрузки, обновление `<title>` и `history.pushState`.

## 🧪 Тесты и Инструменты
- **435 тестов**: 430 Unit для `visuo` + 5 Integration для роутера/ядра. *(См. Roadmap: расширение интеграционных тестов)*
- **PHPStan Level 5** — `[OK] No errors` (baseline 439).
- CI: *в планах (GitHub Actions)*.

## ⚙️ Конфигурация (`config/site.config`)
Переключение движка происходит на уровне сайта (per-site, не per-request):
```json
{
  "logic_type": "alogic",
  "domains_real": { "damoony.ru": "damoony.ru" },
  "domains_namespace": { "damoony.ru": "damoony_ru" }
}
```

## 🗺 Roadmap (Известные архитектурные долги)
1. **PSR-4 для `visuo`**: Переименовать классы (`visuoel_input_hidden` -> `VisuoElInputHidden`) для совместимости с Composer. Оставить старые имена как алиасы.
2. **Расширение тестов**: Добавить Integration-тесты для `auth`, AJAX-ответов и разрешения маршрутов (сейчас их 5).
3. **Снижение baseline**: Поднять PHPStan до Level 6, снизить baseline до 0.
4. **Объединение `available` и `routes.php`**: `available` останется только для `legacy` движка, `alogic` будет использовать только `routes.php`.