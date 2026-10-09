<?php
# =============================================================
#   top_visuo.data.php — данные filler'а top_visuo
#   Контекст: $this = alliasglobal_top_visuo
#   Доступ: $this->top, $this->top->menu, $this->main
# =============================================================

$this->top->wrapper_set( 'div' , [ 'class' => 'top' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'top/top_visuo - блок' );

$this->top->section_add( 'test' , $vt->root( ) );