<?php
# =============================================================
#   right_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_right_robot
# =============================================================

$this->right->wrapper_set( 'div' , [ 'class' => 'right' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/right - блок' );

$this->right->section_add( 'test' , $vt->root( ) );