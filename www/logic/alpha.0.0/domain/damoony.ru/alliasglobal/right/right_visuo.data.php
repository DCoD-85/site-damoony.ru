<?php
# =============================================================
#   right_visuo.data.php — данные filler'а right_visuo
#   Контекст: $this = alliasglobal_right_visuo
# =============================================================

$this->right->wrapper_set( 'div' , [ 'class' => 'right' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'right/right_visuo - блок' );

$this->right->section_add( 'test' , $vt->root( ) );