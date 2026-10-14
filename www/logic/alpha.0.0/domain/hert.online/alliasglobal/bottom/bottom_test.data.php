<?php
# =============================================================
#   bottom_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_bottom_test
# =============================================================

$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/bottom - блок' );

$this->bottom->section_add( 'test' , $vt->root( ) );