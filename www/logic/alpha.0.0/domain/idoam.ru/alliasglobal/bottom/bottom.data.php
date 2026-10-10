<?php
# =============================================================
#   bottom.data.php — данные блока bottom
#   Контекст: $this = alliasglobal_bottom
# =============================================================

$this->wrapper_set( 'div' , [ 'class' => 'bottom' ] );
$this->section_add( 'bottomnav' , $this->make_bottomnav( ) );