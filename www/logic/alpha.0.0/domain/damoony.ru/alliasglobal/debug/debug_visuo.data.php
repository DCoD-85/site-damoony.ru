<?php
# =============================================================
#   debug_visuo.data.php — данные filler'а debug_visuo
#   Контекст: $this = alliasglobal_debug_visuo
# =============================================================

$this->debug->wrapper_set( 'div' , [ 'class' => 'debug' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'debug/debug_visuo - блок' );

$this->debug->section_add( 'test' , $vt->root( ) );