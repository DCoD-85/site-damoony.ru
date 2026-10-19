<?PHP
# ============================================================
#   alliasscheme_www.data.php — структура схемы
# ============================================================
return array (
  'tag' => 'div',
  'class' => 'dt',
  'return' => true,
  'children' => 
  array (
    0 => 
    array (
      'tag' => 'div',
      'class' => 'dt_top',
      'return' => false,
      'enabled' => true,
    ),
    1 => 
    array (
      'tag' => 'div',
      'class' => 'dt_middle',
      'return' => true,
      'enabled' => true,
      'children' => 
      array (
        0 => 
        array (
          'tag' => 'div',
          'class' => 'dt_left',
          'return' => false,
          'enabled' => true,
        ),
        1 => 
        array (
          'tag' => 'div',
          'class' => 'dt_center',
          'return' => false,
          'enabled' => true,
        ),
        2 => 
        array (
          'tag' => 'div',
          'class' => 'dt_right',
          'return' => false,
          'enabled' => true,
        ),
      ),
    ),
    2 => 
    array (
      'tag' => 'div',
      'class' => 'dt_bottom',
      'return' => false,
      'enabled' => true,
    ),
  ),
);
