<?php
# =============================================================
#   right_www.data.php — данные filler'а right_www
#   Контекст: $this = alliasglobal_right_www
# =============================================================

$this->right->wrapper_set( 'div' , [ 'class' => 'right' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'right/right_www - блок' );

$this->right->section_add( 'test' , $vt->root( ) );