<?php
# =============================================================
#   left_visuo.data.php — данные filler'а left_visuo
#   Контекст: $this = alliasglobal_left_visuo
#   Приватные хелперы fill_* живут в left_visuo.php
# =============================================================

$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

$main = $this->main;
$lib  = $main->library;
$url  = $lib->url( );

$url_base     = $url->static_base;
$current_page = (string) ( $url_base[ 1 ] ?? '' );

$menu = $this->left->menu;
$menu->separator = '';

switch ( $current_page ) {
	case 'index'        : $this->fill_index( $menu );        break;
	case 'docs'         : $this->fill_docs( $menu );         break;
	case 'news'         : $this->fill_news( $menu );         break;
	case 'downloads'    : $this->fill_downloads( $menu );    break;
	case 'login'        : $this->fill_login( $menu );        break;
	case 'registration' : $this->fill_registration( $menu ); break;
}