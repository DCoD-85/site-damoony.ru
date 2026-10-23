<?php
# =============================================================
#   center_oxydog.data.php — данные filler'а
#   Контекст: $this = alliasglobal_center_oxydog
# =============================================================

$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'oxydog/center - блок' );

$this->center->section_add( 'test' , $vt->root( ) );