<?php
# =============================================================
#   center_visuo.data.php — данные filler'а center_visuo
#   Контекст: $this = alliasglobal_center_visuo
# =============================================================

$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'center/center_visuo - блок' );

$this->center->section_add( 'test' , $vt->root( ) );