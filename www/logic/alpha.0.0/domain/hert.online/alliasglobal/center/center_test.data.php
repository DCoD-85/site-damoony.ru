<?php
# =============================================================
#   center_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_center_test
# =============================================================

$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/center - блок' );

$this->center->section_add( 'test' , $vt->root( ) );