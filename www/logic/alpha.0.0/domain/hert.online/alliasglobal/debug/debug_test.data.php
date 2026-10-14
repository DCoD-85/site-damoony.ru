<?php
# =============================================================
#   debug_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_debug_test
# =============================================================

$this->debug->wrapper_set( 'div' , [ 'class' => 'debug' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/debug - блок' );

$this->debug->section_add( 'test' , $vt->root( ) );