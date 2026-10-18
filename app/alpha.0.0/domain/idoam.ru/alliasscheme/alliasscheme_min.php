<?PHP

namespace idoam_ru;

class alliasscheme_min {
	# vars
	public null|\main $main = null;
	public null|alliasscheme $alliasscheme = null;
	public null|\visuorelease $visuorelease = null;
    # vars : dt
    public null|\visuotag $dt_root = null;
    public null|\visuotag $dt_top = null;
    public null|\visuotag $dt_middle = null;
    public null|\visuotag $dt_left = null;
    public null|\visuotag $dt_center = null;
    public null|\visuotag $dt_right = null;
    public null|\visuotag $dt_bottom = null;
	# methods
	public function __construct ( alliasscheme $alliasscheme ) {
		$this->alliasscheme = $alliasscheme;
		$this->main = $alliasscheme->main;
		$this->visuorelease = $alliasscheme->main->visuorelease;
	}

	public function meta ( ) {
		
		$main = $this->main;
		$skin = $main->main_settings->skin( 'skin' );
		$allias = $main->main_settings->skin( 'allias' );

		# visuorelease
		$meta = $this->visuorelease->meta( );
		$meta->clear( );
	}

	public function body_preload ( ) {
		
		$vt = new \visuotag( );
		# preload
		$vt->tag( 'div' , class : 'tb_preload' , id : 'tb_preload' )->tag( 'div' , class : 'div' );
		# load
		$vt->tag( 'div' , class : 'tB_load' , id : 'tB_load' )
			->tag( 'div' , class : 'preresult' )
				->tag( 'table' , class : 'preresult_table' )
					->tag( 'tr' )
						->tag( 'td' , class : 'resultpos resultleft' )->parent( )
						->tag( 'td' , class : 'result' )->parent( )
						// <div class="fon" onClick="formblock.dialog.off( );"><div class="note"><div class="notediv">Нажмите, чтобы закрыть окно.</div></div></div><div class="data datablock"></div>
						->tag( 'td' , class : 'resultpos resultright' )->parent( );
		$vt->tag( 'div' , class : 'tB_messages' , id : 'tB_messages' );
		$vt->tag( 'div' , class : 'tB_tmp' );
		$vt->tag( 'div' , class : 'tB_modules' );

		return $vt;
	}

	public function body ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );
		

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'dt_center' );
		
		$visuorelease->visuotag_set( $vt );
	}
    public function clear ( ) {
        $logic = $this;
        
		$logic->dt_root = null;
		$logic->dt_top = null;
		# $logic->dt_middle = null;
		$logic->dt_left = null;
		$logic->dt_center = null;
		$logic->dt_right = null;
		$logic->dt_bottom = null;
    }
    public function init ( ) {
        $main = $this->main;
        $logic = $this;
        $logic->clear( );
		#
		$vt = $main->visuorelease->visuotag( );

		$logic->dt_root = $vt->root( ); // $this->login->root( );
		$logic->dt_top = $logic->dt_root->find( class : 'dt_top' );
		# $logic->dt_middle = $logic->dt_root->find( class : 'dt_middle' );
		$logic->dt_left = $logic->dt_root->find( class : 'dt_left' );
		$logic->dt_center = $logic->dt_root->find( class : 'dt_center' );
		$logic->dt_right = $logic->dt_root->find( class : 'dt_right' );
		$logic->dt_bottom = $logic->dt_root->find( class : 'dt_bottom' );
    }
	
	public function refresh ( bool $reload = false ) {
		$main = $this->main;
		$logic = $this;

		# print( 'asd' . ( $main->alliasglobal->bottom->as_tag( ) ) );
		if( $reload == true ){
			# die( $logic->dt_bottom->release( ) );
		}

		! $logic->dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $logic->dt_left ?: $left = $main->alliasglobal->left->as_tag( );
		! $logic->dt_center ?: $center = $main->alliasglobal->center->as_tag( );
		! $logic->dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $logic->dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		
		! $logic->dt_top ?: $logic->dt_top->clear( )->append( $top );
		! $logic->dt_left ?: $logic->dt_left->clear( )->append( $left );
		! $logic->dt_center ?: $logic->dt_center->clear( )->append( $center );
		! $logic->dt_right ?: $logic->dt_right->clear( )->append( $right );
		! $logic->dt_bottom ?: $logic->dt_bottom->clear( )->append( $bottom );
		# die( $logic->dt_bottom->release( ) );
		
		

	}

	public function load ( ) {
		$this->meta( );
		$this->body( );
		$this->init( );
		$this->refresh( );
	}

	public function reload ( ) {
		$this->visuorelease->meta( )->clear( );
		$this->clear( );
		$this->meta( );
		$this->body( );
		$this->init( );
		$this->refresh( true );
	}
}

?>