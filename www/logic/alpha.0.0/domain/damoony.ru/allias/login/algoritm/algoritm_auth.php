<?PHP

namespace damoony\login;

use \main;
use \algoritm_;
use \algoritmdata_;
use \visuotag;
use \visuorelease;
use \visuoelement;

class algoritm_auth
	extends algoritm_ {
	# vars
	public array $args = [ ];
	# methods
	public function __construct( main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritm = $main->algoritm;
		$this->algoritmdata = new algoritmdata_( );
	}
	public function args ( array $args ) {
		$this->args = $args;
	}
	
	public function algoritm ( ){
	}
	
	public function release ( ) {
		$this->default( );
	}
	
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