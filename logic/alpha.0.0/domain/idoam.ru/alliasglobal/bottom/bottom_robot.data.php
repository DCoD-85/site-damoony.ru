<?php
# =============================================================
#   bottom_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_bottom_robot
# =============================================================

$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/bottom - блок' );

$this->bottom->section_add( 'test' , $vt->root( ) );