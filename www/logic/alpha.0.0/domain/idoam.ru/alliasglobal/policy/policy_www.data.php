<?php
# =============================================================
#   policy_www.data.php — данные filler'а policy_www
#   Контекст: $this = alliasglobal_policy_www
# =============================================================

$this->policy->wrapper_set( 'div' , [ 'class' => 'policy' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'policy/policy_www - блок' );

$this->policy->section_add( 'test' , $vt->root( ) );