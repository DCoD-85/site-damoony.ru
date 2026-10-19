<?PHP
return array (
  'tag' => 'div',
  'class' => 'dt',
  'layout' => 'horizontal',
  'children' => 
  array (
    0 => 
    array (
      'tag' => 'div',
      'class' => 'dt_top',
      'layout' => 'vertical',
      'enabled' => true,
      'return' => false,
      'type' => 'block',
    ),
    1 => 
    array (
      'tag' => 'div',
      'class' => 'dt_middle',
      'layout' => 'vertical',
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
          'type' => 'block',
        ),
        1 => 
        array (
          'tag' => 'div',
          'class' => 'dt_center',
          'layout' => 'horizontal',
          'enabled' => true,
          'children' => 
          array (
            0 => 
            array (
              'tag' => 'div',
              'class' => 'dt_right',
              'layout' => 'vertical',
              'enabled' => true,
              'return' => false,
              'type' => 'block',
            ),
            1 => 
            array (
              'tag' => 'div',
              'class' => 'dt_bottom',
              'layout' => 'vertical',
              'enabled' => true,
              'return' => false,
              'type' => 'block',
            ),
          ),
          'return' => true,
          'type' => 'block',
        ),
      ),
      'return' => true,
      'type' => 'layout',
    ),
  ),
);
