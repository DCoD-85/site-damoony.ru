<?php
# =============================================================
#   right_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_right_test
# =============================================================

$this->right->wrapper_set( 'div' , [ 'class' => 'right' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/right - блок' );

$this->right->section_add( 'test' , $vt->root( ) );