# 💻 Логика: Блоки и Филлеры (alliasglobal)

Сайт в SimplCMS состоит из фиксированных визуальных зон — **Блоков**. 
Вместо того чтобы верстать страницу целиком, вы добавляете контент в нужный блок (например, в `center` или `sidebar`).

## 1. Глобальные блоки (alliasglobal)
По умолчанию все блоки лежат в папке домена: 
`app/.../domain/damoony.ru/alliasglobal/`

Стандартный набор блоков:
*   `top` (Шапка)
*   `left` (Левая колонка)
*   `center` (Основной контент)
*   `right` (Правая колонка)
*   `bottom` (Подвал)

Каждый блок состоит из двух файлов:
1.  **`<block_name>.php`** — логика рендеринга (сборка HTML через Visuo).
2.  **`<block_name>.data.php`** — массив с настройками/данными блока.

## 2. Филлеры (Переопределения)
Если вам нужно, чтобы на аллиасе `visuo` шапка (`top`) выглядела иначе, чем на `www`, вы создаете **Филлер**.

Филлер — это файл вида `<block_name>_<allias>.php`.
Например: `alliasglobal/top/top_visuo.php`.

**Приоритет рендеринга:**
1.  Движок проверяет `config/site.config`. Включен ли филлер для этого аллиаса?
    `fillers -> damoony.ru -> visuo -> top = true`
2.  Если **включен** и файл существует ➔ рендерится `top_visuo.php`.
3.  Если **выключен** или файла нет ➔ рендерится базовый `top.php`.

## 3. Как добавить контент на страницу
Внутри метода `default()` вашего класса страницы вы обращаетесь к глобальному объекту `alliasglobal`.

```php
public function default() {
    // Создаем контент
    $vt = new \visuotag();
    $vt->tag('h1')->content('Мой заголовок');
    
    // Добавляем его в блок center
    // Параметр 'my_section' — это уникальный ID секции (нужен для схем)
    $this->main->alliasglobal->center->section_add('my_section', $vt);
    
    return true;
}
```

## 4. Пример создания филлера

**Шаг 1.** Создайте файл `app/.../alliasglobal/top/top_visuo.php`.
```php
<?PHP
namespace damoony_ru\www;

class alliasglobal_top_visuo extends \alliasglobal_top {
    public function algoritm() {
        // Ваша уникальная логика для аллиаса visuo
        $vt = new \visuotag();
        $vt->tag('div', class: 'special-header')->content('Шапка для Visuo');
        $this->visuotag()->clear();
        $this->visuotag()->append($vt);
    }
}
```

**Шаг 2.** Включите его в `site.config`.
```json
"fillers": {
    "damoony.ru": {
        "visuo": {
            "top": true
        }
    }
}
```

Теперь на аллиасе `visuo` будет отображаться уникальная шапка, а на всех остальных — стандартная.