<?php
# =============================================================
#   debug_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_debug_robot
# =============================================================

$this->debug->wrapper_set( 'div' , [ 'class' => 'debug' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/debug - блок' );

$this->debug->section_add( 'test' , $vt->root( ) );