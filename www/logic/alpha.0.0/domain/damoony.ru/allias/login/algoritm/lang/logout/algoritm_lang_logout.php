<?PHP

class algoritm_lang_logout
	extends algoritm_ {
	# vars
	public algoritm $algoritm;
	public null|algoritmdata_ $algoritmdata = null;
	public visuotag $vt;
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
	public function algoritm ( ) {
	}
	public function release ( ) {
		$main = $this->main;
		if( $main->user->is_guest( ) ){
			$this->algoritmdata->set( );
		} else {
			$this->algoritmdata->set( );
		}
		
		$this->algoritmdata->set( [ '' ] , false , true , $this->main->library->url->static_base[ 2 ] == '' ? true : false );

		# release
		$this->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->main;
		if( $main->user->is_guest( ) ){
			return $this->default_guest( );
		} else {
			return $this->default_logged( );
		}
	}

	public function default_guest ( ) {
		return false;
	}

	public function default_logged ( ) {
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
		
		if( $_POST ){
		} else {
		}
		$main->user->profile->logout( );
		
		if( isset( $_SESSION[ 'login' ][ 'redirect' ] ) ){
			$form_redirect = $_SESSION[ 'login' ][ 'redirect' ];
			unset( $_SESSION[ 'login' ][ 'redirect' ] );
		} else
			$form_redirect = $link->href( $url_domain , [ 'index' ] );
		
		$vt_center = new visuotag( );
		$vt_center->tag( 'div' )->content( 'Вы вышли из учетной записи.' );

		header( 'Location: ' . $form_redirect );

		$center->visuotag( )->append( $vt_center );
	}
	
}

?>