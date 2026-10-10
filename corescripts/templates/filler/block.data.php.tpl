<?php
# =============================================================
#   <block>_<name>.data.php — данные filler'а
#   Контекст: $this = alliasglobal_<block>_<name>
# =============================================================

$this-><block>->wrapper_set( 'div' , [ 'class' => '<block>' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( '<name>/<block> - блок' );

$this-><block>->section_add( 'test' , $vt->root( ) );