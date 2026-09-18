<?PHP

class algoritm_lang_test_linkload
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
			# $this->algoritmdata->set( );
		} else {
			# $this->algoritmdata->set( );
		}
		$this->algoritmdata->set( [ '' ] , false , false , true );

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
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$var = $main->library->vars;
		$library = $main->library;
		$vars = $library->vars;
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
			# vars
			$struct = $main->visuostruct->min( );

			$struct_error = $struct->error( );
			$struct_messages = $struct->messages( );
			$struct_release = $struct->release( );
			$struct_form = $struct->form( );
			$struct_referer = $struct->referer( );
			
			$error = false;
			$messages = $main->messages;
			$release = [ 'html'=>new visuotag( ) , 'layer'=>'' , 'popup'=>false , 'popup_back'=>true , 'popup_navigate'=>false ];
			$form = [ 'reset'=>false , 'redirect'=>null , 'redirect_time'=>null ];
			$referer = null;
			
			$scheme->load( 'min' );
			
			if( ! $_GET[ 'm' ] ){

				$release[ 'html' ]->tag( 'div' , class : 'boxm' )
					->tag( 'div' , class : 'loadbox' )
						->tag( 'div' , class : 'boxmdisplay' )
						->parent( )
						->tag( 'div' , class : 'boxmbody' )
							->tag( 'div' , class : 'boxmrow' , id : 'boxm1' )->content( 'box m1')
							->parent( )
							->tag( 'div' , class : 'boxmrow' , id : 'boxm2' )->content( 'box m2');
				
				$el_link_m1 = new visuoelement( );
				$el_link_m1->link( )->load( $el_link_m1__data = new visuoelement_link_load__data( ));
				$el_link_m1__data->set( 'Link m1' , $link->href( true , true , [ 'm'=>1 ] ) );
				$el_link_m2 = new visuoelement( );
				$el_link_m2->link( )->load( $el_link_m2__data = new visuoelement_link_load__data( ));
				$el_link_m2__data->set( 'Link m2' , $link->href( true , true , [ 'm'=>2 ] ) );

				$vt_link_m1 = new visuotag( );
				$vt_link_m1->tag( 'span' )->content( 'Вы можете выбрать ссылку ' )->element( 'link' , $el_link_m1 )->content( '.' );
				$vt_link_m2 = new visuotag( );
				$vt_link_m2->tag( 'span' )->content( 'Вы можете выбрать ссылку ' )->element( 'link' , $el_link_m2 )->content( '.' );

				$release[ 'html' ]->find( class : 'boxmdisplay' )->append( $vt_link_m1 );
				$release[ 'html' ]->find( class : 'boxmdisplay' )->append( $vt_link_m2 );
				
				$release[ 'layer' ] = 'load_layer';
				$release[ 'popup' ] = $_GET[ 'popup' ] == 'true' ? true : false;
				$release[ 'popup_back' ] = true;
				$release[ 'popup_navigate' ] = true;
			} else if( $_GET[ 'm' ] ){
				$release[ 'html' ]->tag( 'span' , style : 'padding : 20px; background : #EEE ;' )->content( 'New box' . $_GET[ 'm' ] . ' | time: ' . time( ) );
				$release[ 'layer' ] = 'boxm' . $_GET[ 'm' ];
				$release[ 'popup' ] = $_GET[ 'popup' ] == 'true' ? true : false;
				$release[ 'popup_back' ] = true;
				$release[ 'popup_navigate' ] = false;
			}
			
			
			# $messages->add( 'success' , 'Все хорошо.' , 'Загрузка прошла успешно' );
			

			$struct_error->set( $error );
			$struct_messages->set( $messages->as_tag( ) );
			$struct_release->set( $release[ 'html' ] , $release[ 'layer' ] , $release[ 'popup' ] , $release[ 'popup_back' ] , $release[ 'popup_navigate' ] );
			$struct_form->set( $form[ 'reset' ] , $form[ 'redirect' ] , $form[ 'redirect_time' ] );
			$struct_referer->set( $referer );
			
			$visuorelease->release_min( $struct );

			return true;
		} else {
			return false;
		}

		# return
		return true;
	}

	public function default_logged ( ) {
		
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$library = $main->library;
		$vars = $library->vars;
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
			# vars
			$struct = $main->visuostruct->min( );

			$struct_error = $struct->error( );
			$struct_messages = $struct->messages( );
			$struct_release = $struct->release( );
			$struct_form = $struct->form( );
			$struct_referer = $struct->referer( );
			
			$error = false;
			$messages = $main->messages;
			$release = [ 'html'=>new visuotag( ) , 'layer'=>'' , 'popup'=>false , 'popup_back'=>false , 'popup_navigate'=>false ];
			$form = [ 'reset'=>false , 'redirect'=>null , 'redirect_time'=>null ];
			$referer = null;
			
			$scheme->load( 'min' );
			
			$messages->add( 'error' , 'Вы уже авторизованы' , 'Вы уже авторизованы;' );
			$release[ 'html' ] = new visuotag( );
			$release[ 'layer' ] = '';
			$release[ 'popup' ] = false;

			# # # 

			$struct_error->set( $error );
			$struct_messages->set( $messages->as_tag( ) );
			$struct_release->set( $release[ 'html' ] , $release[ 'layer' ] , $release[ 'popup' ] , $release[ 'popup_back' ] , $release[ 'popup_navigate' ] );
			$struct_form->set( $form[ 'reset' ] , $form[ 'redirect' ] , $form[ 'redirect_time' ] );
			$struct_referer->set( $referer );
			
			$visuorelease->release_min( $struct );

			return true;
		} else {
			return false;
		}
	}
}

?>