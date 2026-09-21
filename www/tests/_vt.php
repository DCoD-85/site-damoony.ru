<?PHP
# Мини-хелперы для тестов — сборка visuotag без visuoel.
# Используется вместо удалённого visuoel_primer.

if ( ! function_exists( 'vt_box' ) ) {

function vt_box ( string $class , string $tag = 'div' ) : visuotag {
	$vt = new visuotag( );
	return $vt->tag( $tag , class : $class );
}

function vt_text ( string $content , string $class = 'text' ) : visuotag {
	$vt = new visuotag( );
	$vt->tag( 'div' , class : $class )->content( $content );
	return $vt;
}

function vt_img ( string $src , string $alt = '' , string $class = 'image' ) : visuotag {
	$vt = new visuotag( );
	$img = $vt->tag( 'div' , class : $class )->tag( 'img' );
	$img->attrs_add( 'src' , $src );
	if ( $alt !== '' ) $img->attrs_add( 'alt' , $alt );
	return $vt;
}

function vt_primer ( ) : visuotag {
	return vt_box( 'primer' );
}

}