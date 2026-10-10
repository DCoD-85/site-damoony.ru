<?php
# =============================================================
#   left_www.data.php — данные filler'а left_www
#   Контекст: $this = alliasglobal_left_www
# =============================================================

$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'left/left_www - блок' );

$this->left->section_add( 'test' , $vt->root( ) );