# Visuo — HTML Builder

Независимый fluent-билдер HTML (без Composer, подключается через `require_once`). 

## 🛡 Безопасность (XSS и CSRF)
- **XSS:** Метод `content()` **автоматически экранирует** HTML (`htmlspecialchars`). Для вывода сырого HTML используйте `raw_html()` или `html()`.
- **CSRF:** В формах обязательно используйте встроенный хелпер:
```php
$csrf = new \visuoel_input_hidden();
$csrf->data()->set('_csrf', csrf_token());
$form->add($csrf);
```

## API элементов (фабрика -> билдер)
```php
$ve = new visuoel();

// ->form() создает фабрику, ->form() на ней возвращает сам билдер формы
$form = $ve->form()->form(); 
$form->data()->id('my_form')->method('post')->action('/action');

// Поля: ->field() (фабрика) -> text() (конкретный тип)
$input = $ve->field()->text();
$input->data()->name('login')->label('Логин');
$form->add($input);

echo $form->release();
```

## Интеграция с ядром
`visuo` не зависит от `main`. Для рендера страниц он требует объект, реализующий интерфейс `VisuoHost` (в Damoony2 это делает класс `main`).