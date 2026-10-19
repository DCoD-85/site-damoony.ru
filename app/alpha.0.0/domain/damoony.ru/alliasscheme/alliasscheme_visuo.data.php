<?PHP
# ============================================================
#   alliasscheme_visuo.data.php — структура схемы
# ============================================================
return array (
  'tag' => 'div',
  'class' => 'dt',
  'layout' => 'vertical',
  'children' => 
  array (
    0 => 
    array (
      'tag' => 'div',
      'class' => 'dt_top',
      'layout' => 'vertical',
      'enabled' => true,
      'return' => false,
    ),
    1 => 
    array (
      'tag' => 'div',
      'class' => 'dt_middle',
      'layout' => 'horizontal',
      'enabled' => true,
      'children' => 
      array (
        0 => 
        array (
          'tag' => 'div',
          'class' => 'dt_left',
          'layout' => 'vertical',
          'enabled' => true,
          'return' => false,
        ),
        1 => 
        array (
          'tag' => 'div',
          'class' => 'dt_center',
          'layout' => 'horizontal',
          'enabled' => true,
          'return' => false,
        ),
        2 => 
        array (
          'tag' => 'div',
          'class' => 'dt_right',
          'layout' => 'vertical',
          'enabled' => true,
          'return' => false,
        ),
      ),
      'return' => true,
    ),
    2 => 
    array (
      'tag' => 'div',
      'class' => 'dt_bottom',
      'layout' => 'vertical',
      'enabled' => true,
      'return' => false,
    ),
  ),
);
