<?PHP

return [
    'table'   => 'articles',
    'title'   => 'Статьи',
    'fields'  => [
        'id' => ['type' => 'id'],
        'title' => ['type' => 'string', 'label' => 'Заголовок'],
        'content' => ['type' => 'text', 'label' => 'Текст статьи'],
        'is_published' => ['type' => 'boolean', 'label' => 'Опубликовано', 'default' => 0],
        'created_at' => ['type' => 'datetime', 'label' => 'Дата'],
		'category_id' => [
			'type' => 'relation', 
			'label' => 'Категория',
			'table' => 'categories', // Имя таблицы
			'display' => 'title',    // Поле, которое показывать в списке
		],
    ],
];