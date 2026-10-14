<?php
# =============================================================
#   left_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_left_test
# =============================================================

$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/left - блок' );

$this->left->section_add( 'test' , $vt->root( ) );