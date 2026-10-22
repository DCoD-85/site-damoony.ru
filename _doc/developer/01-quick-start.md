# 💻 Быстрый старт: Первая страница

В этом руководстве мы создадим страницу по адресу `/ru-ru/about` (для аллиаса `www` домена `damoony.ru`), которая будет выводить заголовок и текст.

## Шаг 1. Создайте файл правил (Rules)

Перейдите в папку вашего аллиаса: 
`app/alpha.0.0/domain/damoony.ru/allias/www/algoritm/lang/about/`

Создайте файл `algoritm_lang_about.rules.json` со следующим содержимым:

```json
{
    "available": [],
    "auth": false
}
```

**Что это значит:**
*   `available: []` — у этой страницы нет дочерних URL.
*   `auth: false` — страница доступна всем пользователям (гостям и авторизованным).

## Шаг 2. Создайте PHP-класс страницы

В той же папке создайте файл `algoritm_lang_about.php`.

Обратите внимание на имя класса: оно должно **строго совпадать** с именем файла без расширения `.php`.

```php
<?PHP

namespace damoony_ru\www;

class algoritm_lang_about extends \algoritm_ {
    
    // Этот метод вызывается автоматически, когда пользователь заходит на /ru-ru/about
    public function default() {
        // 1. Создаем контейнер для нашего контента
        $vt = new \visuotag();
        
        // 2. Строим HTML: заголовок и параграф
        $vt->tag('h1')->content('О нашем проекте');
        $vt->tag('p')->content('Добро пожаловать на страницу о нас. Здесь мы рассказываем о SimplCMS.');
        
        // 3. Добавляем этот контент в центральный блок сайта
        $this->main->alliasglobal->center->section_add('about_content', $vt);
        
        // 4. Возвращаем true, сигнализируя, что страница успешно отрисована
        return true;
    }
}
```

## Шаг 3. Зарегистрируйте страницу в родительском узле

Чтобы роутер знал, что страница `about` существует, ее нужно добавить в список доступных URL родительской папки `lang/`.

Откройте файл:
`app/alpha.0.0/domain/damoony.ru/allias/www/algoritm/lang/algoritm_lang.rules.json`

И добавьте `"about"` в массив `available`:

```json
{
    "available": ["index", "about", "test"],
    "auth": false
}
```

## Шаг 4. Проверьте в браузере

Откройте в браузере: `https://www.damoony2.ru/ru-ru/about`

Движок `tree.php`:
1. Увидит сегмент `ru-ru`, перейдет в папку `lang/`.
2. Увидит сегмент `about`, перейдет в папку `lang/about/`.
3. Найдет файл `algoritm_lang_about.php`.
4. Создаст объект класса и вызовет метод `default()`.

Если все сделано правильно, вы увидите заголовок «О нашем проекте» на странице.