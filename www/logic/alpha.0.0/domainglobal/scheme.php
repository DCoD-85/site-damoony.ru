<?PHP
class domainglobal_scheme {
	# vars
	public main $main;
	public visuorelease $visuorelease;
	public visuotag $base;
	public visuotag $warning;
	public visuotag $error;
	##
	public visuotag $index;
	public visuotag $login;
	public visuotag $registration;
	public visuotag $development;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;
		$this->visuorelease = $main->visuorelease;
		
		$skin = $main->main_settings->get( 'skin' );
		
		# visuorelease
		$meta = $this->visuorelease->meta( );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'main.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'top.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'right.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'log.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'debug.css' );
		# visu
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'debug.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'visuo' . DSA . 'visuo.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'visuo' . DSA . 'visuo-form.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'visuo' . DSA . 'visuo-input.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'visuo' . DSA . 'visuo-user.css' );
		$meta->link( )->js( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'js' . DSA . 'jquery-1.8.3.min.js' );
		
		# visuotag
		$this->base = new visuotag( );
		$this->base->tag( '! doctype' , false , return: false )->tag( 'html' , class: 'cl_html' )->tag( 'head' , class: 'cl_head' , return : false )->tag( 'body' , class: 'cl_body' )->root( );
					
		$this->warning = new visuotag( );
		$this->error = new visuotag( );
		
		$this->index = new visuotag( );
		$this->login = new visuotag( );
		$this->registration = new visuotag( );
		$this->development = new visuotag( );
		
		$this->index;
		$this->login->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							->tag( 'div' , class : 'dt_left' , return : false )
							->tag( 'div' , class : 'dt_center' , return : false )
							->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		
		# $bottom = $main->subalgoritm->bottom;
		# $dt_bottom->append( $bottom->as_tag( ) );
	}
	public function start ( string $name ) {
		return $this->{$name};
	}
	public function get ( string $name ) {
		return $this->{$name};
	}
}