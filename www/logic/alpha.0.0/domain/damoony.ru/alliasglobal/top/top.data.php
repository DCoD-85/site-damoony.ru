<?php
# =============================================================
#   top.data.php — данные блока top
#   Контекст: $this = alliasglobal_top
#   Методы-хелперы живут в top.php: fill_*, make_*
# =============================================================

$this->fill_projects( );
$this->fill_login( );
$this->fill_skins( );
$this->fill_languages( );
$this->fill_navigate( );

$this->wrapper_set( 'div' , [ 'class' => 'top' ] );

$this->section_add( 'tophead'  , $this->make_tophead( ) );
$this->section_add( 'toppro'   , $this->make_toppro( ) );
$this->section_add( 'toplinks' , $this->make_toplinks( ) );