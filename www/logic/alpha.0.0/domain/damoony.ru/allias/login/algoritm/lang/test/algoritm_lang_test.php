<?PHP

class algoritm_lang_test
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
		
		$this->algoritmdata->set( [ 'linkload' ] , false , false , false );

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
			$messages = $main->messages;

			$error = false;

			$struct = $main->visuostruct->min( );

			$struct_error = $struct->error( );
			$struct_messages = $struct->messages( );
			$struct_release = $struct->release( );
			$struct_form = $struct->form( );
			$struct_referer = $struct->referer( );
			
			$scheme->load( 'min' );

			$_form = $_POST[ 'form' ];
			$_formcheck = [
				'login'=>[ 'text'=>'Логин' , 'value'=>$_form[ 'login' ] , 'type'=>'string' , 'check'=>$vars->check( $_form[ 'login' ] , 'string' , 5 , 20 ) ] ,
				'password'=>[ 'text'=>'Пароль' , 'value'=>$_form[ 'password' ] , 'type'=>'string' , 'check'=>$vars->check( $_form[ 'password' ] , 'string' , 5 , 100 ) ] ,
				'remember'=>[ 'text'=>'Запомнить' , 'value'=>$_form[ 'remember' ] , 'type'=>'string' , 'check'=>$vars->check( $_form[ 'remember' ] , 'string' , 0 , 100 ) ] ,
			];
			
			foreach( $_formcheck as $key=>$value ){
				$var_field = $value[ 'text' ];
				$var_value = $value[ 'value' ];
				$var_type = $value[ 'type' ];
				$var_check = $value[ 'check' ];
				
				if( $var_type != $var_check ){
					$error = true;
					$messages->add( 'error' , 'Заполните поля правильно: ' . $var_field , 'Заполните поля правильно: [ ' . $var_field . ' ];' );
				}
			}
			
			# Работа с бд
			if( $error == false ){
				$db = $main->db->db( );
				$query = $db->query( );
				$query->select( 'select * from toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ $_formcheck[ 'login' ][ 'value' ] , md5( $_formcheck[ 'password' ][ 'value' ] ) ] , limit : true , limitstep : 1 );
				$results = $query->result( )[ 0 ];
				
				if( ! empty( $results ) ){
					$main->user->profile->login( $results , $_formcheck );
				} else{
					$error = true;
					$messages->add( 'error' , 'Вы ввели не верные учетные данные.' , 'Вы ввели не верные учетные данные.<br />' . $query->show( ) );
				}
			}
			

			if( $error == true ){
				if( ! $_GET[ 'm' ] ){

					$release = [ ];
					$release[ 'html' ] = new visuotag( $visuorelease );
					$release[ 'html' ]->tag( 'div' , class : 'boxm' )
						->tag( 'div' , class : 'loadbox' )
							->tag( 'div' , class : 'boxmdisplay' )
							->parent( )
							->tag( 'div' , class : 'boxmbody' )
								->tag( 'div' , class : 'boxmrow' , id : 'boxm1' )->content( 'box m1')
								->parent( )
								->tag( 'div' , class : 'boxmrow' , id : 'boxm2' )->content( 'box m2');
					
					$el_link_m1 = new visuoelement( );
					$el_link_m1->link( )->load( );
					$el_link_m1__data = $el_link_m1->get_link_load__data( );
					$el_link_m1__data->set( 'Link m1' , $link->href( true , true , [ 'm'=>1 ] ) , 'boxm1' );
					$el_link_m2 = new visuoelement( );
					$el_link_m2->link( )->load( );
					$el_link_m2__data = $el_link_m2->get_link_load__data( );
					$el_link_m2__data->set( 'Link m2' , $link->href( true , true , [ 'm'=>2 ] ) , 'boxm2' );
					$release[ 'html' ]->find( class : 'boxmdisplay' )->element( 'link' , $el_link_m1 );
					$release[ 'html' ]->find( class : 'boxmdisplay' )->element( 'link' , $el_link_m2 );
					
					# $release[ 'html' ]->find( class : 'boxmdisplay' )->tag( 'a' )->attrs_add( 'href' , $link->href( true , true , [ 'm'=>1 ] ) )->content( 'm1' );
					# $release[ 'html' ]->find( class : 'boxmdisplay' )->tag( 'a' )->attrs_add( 'href' , $link->href( true , true , [ 'm'=>2 ] ) )->content( 'm2' );
					$release[ 'layer' ] = '';
					$release[ 'popup' ] = true;
				} else if( $_GET[ 'm' ] ){
					$release = [ ];
					$release[ 'html' ] = new visuotag( $visuorelease );
					$release[ 'html' ]->content( 'New box' . $_GET[ 'm' ] . ' | time: ' . time( ) );
					$release[ 'layer' ] = 'box' . $_GET[ 'm' ];
					$release[ 'popup' ] = true;
				}
			} else {
				$messages->add( 'success' , 'Все хорошо.' , 'SESSION: ' . $_SESSION[ 'login' ][ 'redirect' ] );


				if( isset( $_SESSION[ 'login' ][ 'redirect' ] ) ){
					$form_redirect = $_SESSION[ 'login' ][ 'redirect' ];
					unset( $_SESSION[ 'login' ][ 'redirect' ] );
				} else {
					$form_redirect = $link->href( $url_domain , [ $url_base[ 0 ] , 'redirect' ] );
				}

				# form
				$form[ 'reset' ] = false;
				$form[ 'redirect' ] = $form_redirect;
				$form[ 'redirect_time' ] = 15000;
				# result
				$release[ 'html' ] = new visuotag( $visuorelease );
				$release[ 'html' ]->tag( 'div' )->content( 'RESULT!' );
				$release[ 'layer' ] = 'id_form';
				$release[ 'popup' ] = false;
				
				$messages->add( 'success' , 'Все хорошо.' , 'Вы будете автоматически перенаправлены.<br /><br /><a href="' . $form[ 'redirect' ] . '">Ручное перенаправление</a>' );
			}

			# # # 
			$referer = null;

			$struct_error->set( $error );
			$struct_messages->set( $messages->as_tag( ) );
			$struct_release->set( $release[ 'html' ] , $release[ 'layer' ] , $release[ 'popup' ] );
			$struct_form->set( $form[ 'reset' ] ?: false , $form[ 'redirect' ] ?: null , $form[ 'redirect_time' ] ?: null );
			$struct_referer->set( $referer );
			
			$visuorelease->release_min( $struct );

			return true;
		} else {
			# $this->algoritm->error_( $this );
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
			$messages = $main->messages;

			$error = false;

			$struct = $main->visuostruct->min( );

			$struct_error = $struct->error( );
			$struct_messages = $struct->messages( );
			$struct_release = $struct->release( );
			$struct_form = $struct->form( );
			$struct_referer = $struct->referer( );
			
			$scheme->load( 'min' );
			
			$messages->add( 'error' , 'Вы уже авторизованы' , 'Вы уже авторизованы;' );
			$release[ 'html' ] = new visuotag( );
			$release[ 'layer' ] = '';
			$release[ 'popup' ] = false;

			# # # 
			$referer = null;

			$struct_error->set( $error );
			$struct_messages->set( $messages->as_tag( ) );
			$struct_release->set( $release[ 'html' ] , $release[ 'layer' ] , $release[ 'popup' ] );
			$struct_form->set( $form[ 'reset' ] ?: false , $form[ 'redirect' ] ?: null , $form[ 'redirect_time' ] ?: null );
			$struct_referer->set( $referer );
			
			$visuorelease->release_min( $struct );

			return true;
		} else {
			return false;
		}
	}
}

?>