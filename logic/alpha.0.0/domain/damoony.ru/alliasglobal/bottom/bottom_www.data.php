<?php
# =============================================================
#   bottom_www.data.php — данные filler'а bottom_www
#   Контекст: $this = alliasglobal_bottom_www
# =============================================================

$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'bottom/bottom_www - блок' );

$this->bottom->section_add( 'test' , $vt->root( ) );