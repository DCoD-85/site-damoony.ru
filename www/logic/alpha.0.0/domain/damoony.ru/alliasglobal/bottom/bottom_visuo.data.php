<?php
# =============================================================
#   bottom_visuo.data.php — данные filler'а bottom_visuo
#   Контекст: $this = alliasglobal_bottom_visuo
# =============================================================

$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'bottom/bottom_visuo - блок' );

$this->bottom->section_add( 'test' , $vt->root( ) );