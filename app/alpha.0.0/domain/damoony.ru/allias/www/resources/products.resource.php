<?PHP

return [
    'table'   => 'products',
    'title'   => 'Продукты',
    'fields'  => [
        'id' => ['type' => 'id'],
        'title' => ['type' => 'string', 'label' => 'Заголовок'],
        'content' => ['type' => 'text', 'label' => 'Текст статьи'],
        'is_published' => ['type' => 'boolean', 'label' => 'Опубликовано', 'default' => 0],
        'created_at' => ['type' => 'datetime', 'label' => 'Дата'],
    ],
];