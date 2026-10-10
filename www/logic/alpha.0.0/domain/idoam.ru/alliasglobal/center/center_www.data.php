<?php
# =============================================================
#   center_www.data.php — данные filler'а center_www
#   Контекст: $this = alliasglobal_center_www
# =============================================================

$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'center/center_www - блок' );

$this->center->section_add( 'test' , $vt->root( ) );