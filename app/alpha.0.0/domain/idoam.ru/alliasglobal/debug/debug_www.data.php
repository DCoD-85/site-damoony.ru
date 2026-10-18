<?php
# =============================================================
#   debug_www.data.php — данные filler'а debug_www
#   Контекст: $this = alliasglobal_debug_www
# =============================================================

$this->debug->wrapper_set( 'div' , [ 'class' => 'debug' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'debug/debug_www - блок' );

$this->debug->section_add( 'test' , $vt->root( ) );