<?php
# =============================================================
#   center_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_center_robot
# =============================================================

$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/center - блок' );

$this->center->section_add( 'test' , $vt->root( ) );