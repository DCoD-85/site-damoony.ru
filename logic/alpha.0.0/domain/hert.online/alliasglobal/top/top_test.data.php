<?php
# =============================================================
#   top_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_top_test
# =============================================================

$this->top->wrapper_set( 'div' , [ 'class' => 'top' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/top - блок' );

$this->top->section_add( 'test' , $vt->root( ) );