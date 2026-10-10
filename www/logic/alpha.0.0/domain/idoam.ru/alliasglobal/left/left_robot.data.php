<?php
# =============================================================
#   left_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_left_robot
# =============================================================

$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/left - блок' );

$this->left->section_add( 'test' , $vt->root( ) );