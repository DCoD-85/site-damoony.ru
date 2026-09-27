<?PHP

namespace damoony\www;

use \main;
use \algoritm_;
use \algoritmdata_;
use \visuotag;
use \visuorelease;
use \visuoelement;

class algoritm_auth
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
	#   DEFAULT — сообщение «нужна авторизация»
	# ---------------------------------------------------------------
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		$top = $main->alliasglobal->center;
		$center = $main->alliasglobal->center;

		$scheme = $main->alliasscheme;
		$scheme->load( 'error' );

		$vt_center = new visuotag( $main->visuorelease );
		$vt_center->tag( 'div' )->content( 'Вы должны быть зарегистрированы для просмотра данной страницы' );

		$center->visuotag( )->append( $vt_center );
	}
}

?>