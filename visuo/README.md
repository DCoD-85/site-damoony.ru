# Visuo — HTML Builder

Независимый fluent-билдер HTML.

## 🛡 Безопасность
- `content()` **автоматически экранирует** HTML.
- Для сырого HTML используйте `raw_html()`.
- Для CSRF используйте `visuoel_input_hidden` и `csrf_token()`.

## 🗺 Roadmap: PSR-4
Сейчас классы используют `snake_case` (`visuoel_input_hidden`). В будущем они будут переименованы в `VisuoElInputHidden` (PSR-4) с сохранением старых имен как алиасов.

## Пример использования
```php
$ve = new visuoel();
$form = $ve->form()->form();
$form->data()->id('my_form')->method('post')->action('/action');

$input = $ve->field()->text();
$input->data()->name('login');
$form->add($input);

echo $form->release();
```