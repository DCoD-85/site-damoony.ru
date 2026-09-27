<?PHP

namespace damoony\www;

use \main;
use \algoritm_;
use \algoritmdata_;
use \visuotag;
use \visuoelement;

class algoritm_error
	extends algoritm_ {

	# ---------------------------------------------------------------
	#   ALGORITM — пустой (стаб)
	# ---------------------------------------------------------------
	public function algoritm ( ) {
	}

	# ---------------------------------------------------------------
	#   RELEASE — минуя систему маршрутизации, сразу в default()
	# ---------------------------------------------------------------
	public function release ( ) {
		$this->default( );
	}

	# ---------------------------------------------------------------
	#   DEFAULT — страница 404
	# ---------------------------------------------------------------
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;

		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		$center = $main->alliasglobal->center;

		$headers = $main->alliasglobal->headers;
		$scheme = $main->alliasscheme;

		$scheme->load( 'error' );

		$vt_center = new visuotag( $this->algoritm->main->visuorelease );
		$vt_center
			->tag( 'div' , class : 'error' , style : '' )
				->tag( 'div' , class : 'error_head' )->content( '404' )
				->parent( )
				->tag( 'div' , class : 'error_title' )->content( 'Page not found<hr />Страница не найдена' );

		$center->visuotag( )->append( $vt_center );
	}
}

?>