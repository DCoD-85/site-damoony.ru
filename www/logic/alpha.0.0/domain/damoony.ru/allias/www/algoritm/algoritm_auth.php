<?PHP

class algoritm_auth {
	# vars
	public algoritm $algoritm;
	public array $args = [ ];
	# methods
	public function __construct ( algoritm $algoritm ) {
		$this->algoritm = $algoritm;
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
		
		$scheme = $main->alliasglobal->scheme;
		$scheme->load( 'auth' );

		$vt_center = new visuotag( $this->visuorelease );
		$vt_center->tag( 'div' )->content( 'Вы должны быть зарегистрированы для просмотра данной страницы' );

		$center->visuotag( )->append( $vt_center );
	}
	
}

?>