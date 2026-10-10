<?php
# =============================================================
#   top_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_top_robot
# =============================================================

$this->top->wrapper_set( 'div' , [ 'class' => 'top' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/top - блок' );

$this->top->section_add( 'test' , $vt->root( ) );