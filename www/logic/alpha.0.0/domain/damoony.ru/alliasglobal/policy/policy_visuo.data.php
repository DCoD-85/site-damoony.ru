<?php
# =============================================================
#   policy_visuo.data.php — данные filler'а policy_visuo
#   Контекст: $this = alliasglobal_policy_visuo
# =============================================================

$this->policy->wrapper_set( 'div' , [ 'class' => 'policy' ] );

$vt = new \visuotag( );
$vt->tag( 'div' , class : 'test-block' )->content( 'policy/policy_visuo - блок' );

$this->policy->section_add( 'test' , $vt->root( ) );