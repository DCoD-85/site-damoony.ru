<?php
# =============================================================
#   debug_oxydog.data.php — данные filler'а
#   Контекст: $this = alliasglobal_debug_oxydog
# =============================================================

$this->debug->wrapper_set( 'div' , [ 'class' => 'debug' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'oxydog/debug - блок' );

$this->debug->section_add( 'test' , $vt->root( ) );