<?PHP

namespace damoony_ru\visuo;

use \main;
use \algoritm_;
use \algoritmdata_;
use \visuotag;

class algoritm_error
	extends algoritm_ {

	public function algoritm ( ) {
	}

	public function release ( ) {
		$this->default( );
	}

	public function default ( ) {
		http_response_code( 404 );

		$main = $this->main;
		$visuorelease = $main->visuorelease;

		$center = $main->alliasglobal->center;

		$scheme = $main->alliasscheme;
		$scheme->load( 'error' );

		$vt_center = new visuotag( $visuorelease );
		$vt_center
			->tag( 'div' , class : 'error' )
				->tag( 'div' , class : 'error_head' )->content( '404' )
				->parent( )
				->tag( 'div' , class : 'error_title' )->content( 'Page not found<hr />Страница не найдена' );

		$center->visuotag( )->append( $vt_center );
	}
}