<?PHP
# ============================================================
#   alliasscheme_www.data.php — структура схемы (каркас)
#   return : true — следующий тег вложенный, false — соседний
#   enabled : false — блок не выводится в DOM
# ============================================================
return [
	'tag'    => 'div',
	'class'  => 'dt',
	'return' => true,
	'children' => [
		[
			'tag'    => 'div',
			'class'  => 'dt_top',
			'return' => false,
			'enabled' => true,
		],
		[
			'tag'    => 'div',
			'class'  => 'dt_middle',
			'return' => true,
			'enabled' => true,
			'children' => [
				[
					'tag'    => 'div',
					'class'  => 'dt_left',
					'return' => false,
					'enabled' => true,
				],
				[
					'tag'    => 'div',
					'class'  => 'dt_center',
					'return' => false,
					'enabled' => true,
				],
				[
					'tag'    => 'div',
					'class'  => 'dt_right',
					'return' => false,
					'enabled' => true,
				],
			],
		],
		[
			'tag'    => 'div',
			'class'  => 'dt_bottom',
			'return' => false,
			'enabled' => true,
		],
	],
];
