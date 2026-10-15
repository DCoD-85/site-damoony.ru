<?PHP

namespace damoony_ru\visuo;

use \main;
use \algoritm_;
use \algoritmdata_;
use \visuotag;
use \visuorelease;

class algoritm_auth
	extends algoritm_ {

	public function algoritm ( ) {
	}

	public function release ( ) {
		$this->default( );
	}

		public function default ( ) {
		http_response_code( 401 );

		$main = $this->main;
		$visuorelease = $main->visuorelease;
		$center = $main->alliasglobal->center;

		$scheme = $main->alliasscheme;
		$scheme->load( 'login' );

		$vt_center = new visuotag( $visuorelease );
		$vt_center->tag( 'div' )->content( 'Вы должны быть зарегистрированы для просмотра данной страницы' );

		$center->visuotag( )->append( $vt_center );
	}
}