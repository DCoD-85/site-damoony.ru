<?php
# =============================================================
#   policy_robot.data.php — данные filler'а
#   Контекст: $this = alliasglobal_policy_robot
# =============================================================

$this->policy->wrapper_set( 'div' , [ 'class' => 'policy' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'robot/policy - блок' );

$this->policy->section_add( 'test' , $vt->root( ) );