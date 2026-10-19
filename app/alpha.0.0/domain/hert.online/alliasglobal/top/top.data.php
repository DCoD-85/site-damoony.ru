<?php
# top.data.php — данные блока top
# Контекст: $this = alliasglobal_top

$this->wrapper_set( 'div' , [ 'class' => 'top' ] );

$this->section_add( 'tophead'  , $this->make_tophead( ) );
$this->section_add( 'toppro'   , $this->make_toppro( ) );
$this->section_add( 'toplinks' , $this->make_toplinks( ) );