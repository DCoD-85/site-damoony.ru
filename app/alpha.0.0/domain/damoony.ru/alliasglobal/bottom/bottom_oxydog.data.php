<?php
# =============================================================
#   bottom_oxydog.data.php — данные filler'а
#   Контекст: $this = alliasglobal_bottom_oxydog
# =============================================================

$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'oxydog/bottom - блок' );

$this->bottom->section_add( 'test' , $vt->root( ) );