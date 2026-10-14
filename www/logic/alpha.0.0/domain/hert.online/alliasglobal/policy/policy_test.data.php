<?php
# =============================================================
#   policy_test.data.php — данные filler'а
#   Контекст: $this = alliasglobal_policy_test
# =============================================================

$this->policy->wrapper_set( 'div' , [ 'class' => 'policy' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'test/policy - блок' );

$this->policy->section_add( 'test' , $vt->root( ) );