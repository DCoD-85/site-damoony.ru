<?PHP

namespace damoony;

class alliasglobal_scheme {
	# vars
	public \main $main;
	public \visuorelease $visuorelease;
	public string $schemename = '';
	public string $default = 'index';
	public \visuotag $scheme_error;
	##
	public \visuotag $scheme_min;
	public \visuotag $scheme_index;
	public \visuotag $scheme_development;
	public \visuotag $scheme_login;
	# methods
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->visuorelease = $main->visuorelease;
		
		$this->scheme_error = new \visuotag( );
		$this->scheme_min = new \visuotag( );
		$this->scheme_index = new \visuotag( );
		$this->scheme_development = new \visuotag( );
		$this->scheme_login = new \visuotag( );
	}
	
	public function init ( ) {
		$main = $this->main;
	}

	public function show( ){
		return 'Scheme: ' . $this->schemename . '<br />';
	}
	
	# public function start ( string $name ) {
	# 	$this->{$name.'_reload'}( );
		# return $this->{$name.'_reload'};
	# }
	public function get ( string $name ) {
		return $this->{$name};
	}
	
	public function load ( string $scheme ) {
		$main = $this->main;
		$this->schemename = $scheme;

		if( ! method_exists( $this , 'scheme_' . $scheme ) ) $scheme = $this->default;
		
		$_meta = 'scheme_' . $scheme . '_meta'; 
		$_visuotag = 'scheme_' . $scheme . '_visuotag'; 
		$_ = 'scheme_' . $scheme;
		
		if( ! method_exists( $this , $_meta ) ) print 'BAD: ' . $_meta . '<br />';
		if( ! method_exists( $this , $_visuotag ) ) print 'BAD: ' . $_visuotag . '<br />';
		if( ! method_exists( $this , $_ ) ) print 'BAD: ' . $_ . '<br />';

		$this->{$_meta}( );
		$this->{$_visuotag}( );
		$this->{$_}( );

			$this->main->algoritm->logic->init( );
		
		if( $scheme == 'error2' ){
			# print $this->visuorelease->meta( )->as_tag( )->release( );

			# die( ( $_ ) );
		} else {
		}
	}

	public function reload ( ) {
		$schemename = $this->schemename;
		
		$_ = 'scheme_' . $schemename;
		
		# die( $_ );
		
		$this->{$_}( true );
	}
	
	public function tmp_meta ( string $allias_name ) {
		$main = $this->main;
		$skin = $main->main_settings->skin( 'skin' );
		$allias = $main->main_settings->skin( 'allias' );

		# visuorelease
		$meta = $this->visuorelease->meta( );
		$meta->clear( );
		$meta->link( )->favicon( '/media/favicon.ico#' . $allias_name );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'main.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'vardump.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'messages.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'popup.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'top.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'left.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . $allias . DSA . 'dt.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'right.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . 'bottom.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . '_' . DSA . 'log.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'css' . DSA . '_' . DSA . 'debug.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo-elements-block.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo-elements-table.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo-elements-form.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo-elements-input.css' );
		$meta->link( )->css( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'visuo' . DSA . 'visuo-elements.css' );
		$meta->link( )->js( DSA . 'media' . DSA . 'release' . DSA . $skin . DSA . 'js' . DSA . 'jquery-1.8.3.min.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'messages.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine_popup.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine_prepare.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine_load.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine_visuotablebox.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine.js' );
		$meta->link( )->js( DSA . 'media' . DSA . 'js' . DSA . 'jsengine' . DSA . 'engine_ready.js' );
	}

	# # # META
	public function scheme_error_meta ( ) {
		$this->tmp_meta( 'error' );
	}
	public function scheme_auth_meta ( ) {
		$this->tmp_meta( 'error' );
	}
	public function scheme_min_meta ( ) {
	}
	public function scheme_index_meta ( ) {
		$this->tmp_meta( 'www' );
	}
	public function scheme_development_meta ( ) {
		$this->tmp_meta( 'www' );
	}
	public function scheme_login_meta ( ) {
		$this->tmp_meta( 'login' );
	}

	public function scheme__tmpbody ( ) {
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
	# # # visuotag		
	public function scheme_error_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );
		
		$vt = new \visuotag( );
		$vt->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->append( $this->scheme__tmpbody( ) )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							->tag( 'div' , class : 'dt_left' , return : false )
							->tag( 'div' , class : 'dt_center' , return : false )
							->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		$visuorelease->visuotag_set( $vt );
	}
	public function scheme_auth_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );
		
		$vt = new \visuotag( );
		$vt->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->append( $this->scheme__tmpbody( ) )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							# ->tag( 'div' , class : 'dt_left' , return : false )
							->tag( 'div' , class : 'dt_center' , return : false )
							# ->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		$visuorelease->visuotag_set( $vt );
	}
	
	public function scheme_min_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'dt_center' );
		
		$visuorelease->visuotag_set( $vt );
	}

	public function scheme_index_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );

		$vt = new \visuotag( );
		$vt->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html scheme_login' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->append( $this->scheme__tmpbody( ) )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							->tag( 'div' , class : 'dt_left' , return : false )
							->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		$visuorelease->visuotag_set( $vt );
	}

	public function scheme_development_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );

		$vt = new \visuotag( );
		$vt->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html scheme_login' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->append( vt: $this->scheme__tmpbody( ) )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							->tag( 'div' , class : 'dt_left' , return : false )
							->tag( 'div' , class : 'dt_center' , return : false )
							->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		$visuorelease->visuotag_set( $vt );
	}

	public function scheme_login_visuotag ( ) {
		$main = $this->main;
		$visuorelease = $this->visuorelease;
		$visuorelease->visuotag_clear( );
		

		$vt = new \visuotag( );
		$vt->tag( '! doctype' , false , return : false )
			->tag( 'html' , class : 'cl_html scheme_login' )
				->tag( 'head' , class : 'cl_head' , return : false )
				->tag( 'body' , class : 'cl_body' )
					->append( vt: $this->scheme__tmpbody( ) )
					->tag( 'div' , id : 'popup_result' )->parent( )
					->tag( 'div' , class : 'dt' )
						->tag( 'div' , class : 'dt_top' , return : false )
						->tag( 'div' , class : 'dt_middle' )
							->tag( 'div' , class : 'dt_center' , return : false )
							->tag( 'div' , class : 'dt_right' , return : false )
						->parent( )
						->tag( 'div' , class : 'dt_bottom' , return : false )
					->parent( )->tag( 'div' , class : 'dt_debug' , return : true );
		
		$visuorelease->visuotag_set( $vt );
	}
	
	# 
	public function scheme_error ( ) {
		$main = $this->main;
		
		#
		$vt = $main->visuorelease->visuotag( );
		$dt_root = $vt;
		
		$dt_top = $dt_root->find( class : 'dt_top' );
		$dt_left = $dt_root->find( class : 'dt_left' );
		$dt_right = $dt_root->find( class : 'dt_right' );
		$dt_bottom = $dt_root->find( class : 'dt_bottom' );
		
		! $dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $dt_left ?: $left = $main->alliasglobal->left->as_tag();
		# ! $dt_center ?: $right = $main->alliasglobal->center->as_tag( );
		! $dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		
		
		! $dt_top ?: $dt_top->clear( )->append( $top );
		! $dt_left ?: $dt_left->clear( )->append( $left );
		# ! $dt_center ?: $dt_center->clear( )->append( $center );
		! $dt_right ?: $dt_right->clear( )->append( $right );
		! $dt_bottom ?: $dt_bottom->clear( )->append( $bottom );
		
	}
	public function scheme_auth ( ) {
		$main = $this->main;
		
		#
		$vt = $main->visuorelease->visuotag( );
		$dt_root = $vt;
		
		$dt_top = $dt_root->find( class : 'dt_top' );
		$dt_left = $dt_root->find( class : 'dt_left' );
		$dt_right = $dt_root->find( class : 'dt_right' );
		$dt_bottom = $dt_root->find( class : 'dt_bottom' );
		
		! $dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $dt_left ?: $left = $main->alliasglobal->left->as_tag( );
		# ! $dt_center ?: $right = $main->alliasglobal->center->as_tag( );
		! $dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		
		
		! $dt_top ?: $dt_top->append( $top );
		! $dt_left ?: $dt_left->append( $left );
		# ! $dt_center ?: $dt_center->append( $center );
		! $dt_right ?: $dt_right->append( $right );
		! $dt_bottom ?: $dt_bottom->append( $bottom );
		
	}
	public function scheme_min ( ) {
	}
	public function scheme_index ( ) {
		$main = $this->main;
		
		#
		$vt = $main->visuorelease->visuotag( );
		$dt_root = $vt;
		
		$dt_top = $dt_root->find( class : 'dt_top' );
		$dt_left = $dt_root->find( class : 'dt_left' );
		$dt_right = $dt_root->find( class : 'dt_right' );
		$dt_bottom = $dt_root->find( class : 'dt_bottom' );
		
		! $dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $dt_left ?: $left = $main->alliasglobal->left->as_tag( );
		! $dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		
		
		! $dt_top ?: $dt_top->append( $top );
		! $dt_left ?: $dt_left->append( $left );
		# $dt_center->content( 'CENTER' );
		! $dt_right ?: $dt_right->append( $right );
		! $dt_bottom ?: $dt_bottom->append( $bottom );
	}
	public function scheme_development ( ) {
		$main = $this->main;
		
		#
		$vt = $main->visuorelease->visuotag( );
		$dt_root = $vt;
		
		$dt_top = $dt_root->find( class : 'dt_top' );
		$dt_left = $dt_root->find( class : 'dt_left' );
		$dt_right = $dt_root->find( class : 'dt_right' );
		$dt_bottom = $dt_root->find( class : 'dt_bottom' );
		
		! $dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $dt_left ?: $left = $main->alliasglobal->left->as_tag( );
		! $dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		
		
		! $dt_top ?: $dt_top->append( $top );
		! $dt_left ?: $dt_left->append( $left );
		# $dt_center->content( 'CENTER' );
		! $dt_right ?: $dt_right->append( $right );
		! $dt_bottom ?: $dt_bottom->append( $bottom );
	}
	public function scheme_login ( bool $reload = false ) {
		$main = $this->main;
		
		#
		$vt = $main->visuorelease->visuotag( );
		$dt_root = $vt;

		$dt_top = $dt_root->find( class : 'dt_top' );
		$dt_left = $dt_root->find( class : 'dt_left' );
		$dt_center = $dt_root->find( class : 'dt_center' );
		$dt_right = $dt_root->find( class : 'dt_right' );
		$dt_bottom = $dt_root->find( class : 'dt_bottom' );
		
		! $dt_top ? : $top = $main->alliasglobal->top->as_tag( );
		! $dt_left ?: $left = $main->alliasglobal->left->as_tag( );
		! $dt_center ?: $center = $main->alliasglobal->center->as_tag( );
		! $dt_right ?: $right = $main->alliasglobal->right->as_tag( );
		! $dt_bottom ?: $bottom = $main->alliasglobal->bottom->as_tag( );
		

		if( $reload === true ){
			# print $bottom->prepare( );
			# print $bottom->release( );
		}

		! $dt_top ?: $dt_top->clear( )->append( $top );
		! $dt_left ?: $dt_left->clear( )->append( $left );
		! $dt_center ?: $dt_center->clear( )->append( $center );
		! $dt_right ?: $dt_right->clear( )->append( $right );
		! $dt_bottom ?: $dt_bottom->clear( )->append( $bottom );

	}
}