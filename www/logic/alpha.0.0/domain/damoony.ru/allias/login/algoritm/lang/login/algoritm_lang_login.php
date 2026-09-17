<?PHP

class algoritm_lang_login
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
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		
		$var = $main->core->var;
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

			$_form = $_POST[ 'form' ];
			$_formcheck = [
				'login'=>[ $_form[ 'login' ] , $var->check( $_form[ 'login' ] , 'string' , 5 , 30 ) , 'Логин' ] ,
				'password'=>[ $_form[ 'password' ] , $var->check( $_form[ 'password' ] , 'string' , 5 , 100 ) , 'Пароль' ] ,
				'remember'=>[ $_form[ 'remember' ] , $var->check( $_form[ 'remember' ] , 'string' , 0 , 10 ) , 'Запомнить' ] ,
				'submit'=>[ $_form[ 'submit' ] , $var->check( $_form[ 'submit' ] , 'string' , 0 , 20 ) , 'Подтвердить' ] ,
			];
			
			foreach( $_formcheck as $key=>$value ){
				$value_var = $value[ 0 ];
				$value_check = $value[ 1 ];
				$value_field = $value[ 2 ];

				if( $value_check == false ) {
					$error = true;
					$var->error( $value_field );
				}
			}
			
			# Работа с бд
			if( $error == false ){
				$db = $main->db->db( );
				$query = $db->query( );
				$query->select( 'select * from toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ $_formcheck[ 'login' ][ 0 ] , md5( $_formcheck[ 'password' ][ 0 ] ) ] , limit : true , limitstep : 1 );
				$results = $query->result( )[ 0 ];
				
				if( ! empty( $results ) ){
					$main->user->profile->login( $results , $_formcheck );
				} else{
					$error = true;
					$messages->add( 'error' , 'Вы ввели не верные учетные данные.' , 'Вы ввели не верные учетные данные.<br />' . $query->show( ) );
				}
			}
			

			if( $error == true ){
				$release[ 'html' ];
				$release[ 'layer' ] = '';
				$release[ 'popup' ] = false;
				$release[ 'popup_back' ] = false;
				$release[ 'popup_navigate' ] = false;
			} else {

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
				$release[ 'html' ]->tag( 'div' )->content( 'RESULT!' );
				$release[ 'layer' ] = '';
				$release[ 'popup' ] = false;
				
				$messages->add( 'success' , 'Все хорошо.' , 'Вы будете автоматически перенаправлены.<br /><br /><a href="' . $form[ 'redirect' ] . '">Ручное перенаправление</a>' );
			}

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
			$messages = $main->messages;

			$error = false;

			$struct = $main->visuostruct->min( );

			$struct_error = $struct->error( );
			$struct_messages = $struct->messages( );
			$struct_release = $struct->release( );
			$struct_form = $struct->form( );
			$struct_referer = $struct->referer( );
			
			$error = false;
			$messages = $main->messages;
			$release = [ 'html'=>new visuotag( ) , 'layer'=>null , 'popup'=>null , 'popup_opt'=>null ];
			$form = [ 'reset'=>false , 'redirect'=>null , 'redirect_time'=>null ];
			$referer = false;

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