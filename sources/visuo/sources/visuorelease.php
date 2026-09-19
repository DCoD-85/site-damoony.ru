<?PHP

class visuorelease {
	# --- refs ---
	public null|\main $main = null;
	public \visuolog  $visuolog;
	public \visuorelease_settings $settings;

	# --- state ---
	public null|\visuometa $visuometa  = null;
	public null|\visuotag  $visuotag   = null;
	public null|\visuotag  $visuotag_final = null;
	public string          $release_alternative = '';

	# --- methods ---
	public function __construct ( null|\main $main = null ,
	                              null|\visuorelease_settings $settings = null ) {
		$this->visuolog  = new visuolog( );
		$this->main      = $main;
		$this->settings  = $settings ?? new visuorelease_settings( $this );

		$this->visuometa     = new visuometa( $this );
		$this->visuotag      = new visuotag( $this );
		$this->visuotag_final = new visuotag( $this );

		$this->init( );
	}

	public function settings ( ) : visuorelease_settings {
		return $this->settings;
	}

	public function init ( ) : void {
		$this->schemenone_init( );
	}

	# без схемы: doctype + html + head + body
	private function schemenone_init ( ) : void {
		$this->visuotag
			->tag( '! doctype' , false , return: false )
			->tag( 'html' , class: 'cl_html' )
			->tag( 'head' , class: 'cl_head' , return: false )
			->tag( 'body' , class: 'cl_body' )
			->root( );
	}

	public function meta ( ) : null|visuometa {
		return $this->visuometa;
	}

	public function visuotag ( ) : visuotag {
		return $this->visuotag->_body( ) ?: $this->visuotag;
	}

	public function visuotag_set ( visuotag $visuotag ) : visuorelease {
		$this->visuotag = $visuotag;
		return $this;
	}

	public function visuotag_clear ( ) : void {
		$this->visuotag->root( )->clear( );
		$this->visuotag_final->root( )->clear( );
	}

	# ------------------------------------------------------------
	#  Release
	# ------------------------------------------------------------

	private function release ( bool $log = false , bool $debug = false ) : string {
		# встроить meta в <head>
		$this->visuotag->root( )->find( 'head' )->meta( $this->visuometa );

		# подготовить и релизнуть
		$this->visuotag->prepare( );
		$this->visuotag->release( );

		$this->visuotag_final = $this->visuotag;

		# отладочная панель
		$body = $this->visuotag_final->body( );

		$end    = microtime( true );
		$start  = $GLOBALS[ 'start' ] ?? $end;
		$number = ( $end - $start ) * 1000;

		if ( $log === true || $debug === true ) {
			$deb = new visuotag( );
			$deb
				->tag( 'div' , class : 'debugging hidden' )
					->tag( 'div' , class : 'debug_head' )
						->tag( 'div' , class : 'debug_head_title' , return : false )
						->tag( 'div' , class : 'debug_head_link'  , return : false )
						->tag( 'div' , class : 'debug_head_time'  , return : false )
						->parent( )
						->tag( 'div' , id : 'debug_log'  , class : 'debug_log'  )
						->parent( )
						->tag( 'div' , id : 'debug_tree' , class : 'debug_tree' )
						->root( );

			$deb_title = $deb->find( class : 'debug_head_title' );
			$deb_link  = $deb->find( class : 'debug_head_link'  );
			$deb_time  = $deb->find( class : 'debug_head_time'  );
			$deb_log   = $deb->find( class : 'debug_log'        );
			$deb_tree  = $deb->find( class : 'debug_tree'       );

			$deb_title->content( 'Debugging' );
			$deb_time
				->attrs_actions_add( 'onclick' , 'd = $(\'.debugging\'); if( d.hasClass(\'hidden\') ){d.removeClass( \'hidden\' )}else{d.addClass( \'hidden\');}' )
				->content( round( $number , 3 ) . 'ms' );

			$deb_link
				->tag( 'a' )
					->attrs_add( 'href' , 'javascript:;' )
					->attrs_actions_add( 'onClick' , 'dl = $(\'#debug_log\'); dt = $(\'#debug_tree\'); dl.show( ); dt.hide( );' )
					->content( 'Log' );

			$deb_link->tag( 'div' , class : 'between' )->content( '|' );

			$deb_link
				->tag( 'a' )
					->attrs_add( 'href' , 'javascript:;' )
					->attrs_actions_add( 'onClick' , 'dl = $(\'#debug_log\'); dt = $(\'#debug_tree\'); dl.hide( ); dt.show( );' )
					->content( 'Debug' );

			if ( $log === true ) {
				$deb_log->append( $this->visuolog->as_tag( ) );
			}

			if ( $debug === true ) {
				$vt_debug = $body->root( )->releasetree( );
				$deb_tree->content( $vt_debug );
			}

			$dt_debug = $body->find( 'div' , class : 'dt_debug' );
			if ( $dt_debug !== null ) $dt_debug->append( $deb );
		} else {
			$vt_debug = new visuotag( );
			$vt_debug->tag( 'div' , class : 'debugging_onlytime' )->content( round( $number , 3 ) . 'ms' );

			$dt_debug = $body->find( 'div' , class : 'dt_debug' );
			if ( $dt_debug !== null ) $dt_debug->append( $vt_debug );
		}

		return $this->visuotag_final->final( );
	}

	# ------------------------------------------------------------
	#  Alternative releases (min / ajax)
	# ------------------------------------------------------------

	public function release_min ( null|\visuostruct_min $struct = null ,
	                              bool $min = true ,
	                              bool $json = true ) : void {
		$this->main->render->setdata( 'min' , $min );

		if ( $struct === null ) return;

		$release_json = [
			'error'    => $struct->error(    )->release( ) ,
			'messages' => $struct->messages( )->release( ) ,
			'release'  => $struct->release(  )->release( ) ,
			'form'     => $struct->form(     )->release( ) ,
			'referer'  => $struct->referer(  )->release( ) ,
		];

		$this->release_alternative = $json ? ( json_encode( $release_json ) ?: '' ) : '';
	}

	public function release_struct ( null|\visuostruct_ajax $struct = null ,
	                                 bool $min = true ,
	                                 bool $json = true ) : void {
		$this->main->render->setdata( 'min' , $min );

		if ( $struct === null ) return;

		$this->release_alternative = $struct->as_json( );
	}

	private function release_alternative_value ( ) : string {
		return $this->release_alternative;
	}

	# ------------------------------------------------------------
	#  Final
	# ------------------------------------------------------------

	public function final ( bool $log = false , bool $debug = false ) : void {
		if ( $this->main->render->getdata( 'min' ) == false ) {
			print $this->release( $log , $debug );
		} else {
			print $this->release_alternative_value( );
		}
	}

	public function final_ajax ( bool $log = false , bool $debug = false ) : void {
		$html = $this->visuotag->root( )->find( 'html' );
		if ( $html !== null ) {
			$html->data_add( 'ajax'      , true  );
			$html->data_add( 'ajax-init' , false );
		}

		if ( $this->main->render->getdata( 'min' ) == false ) {
			print $this->release( $log , $debug );
		} else {
			print $this->release_alternative_value( );
		}
	}
}