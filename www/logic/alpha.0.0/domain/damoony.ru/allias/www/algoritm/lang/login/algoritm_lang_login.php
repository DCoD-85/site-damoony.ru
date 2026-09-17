<?PHP

class algoritm_lang_login {
	# vars
	public algoritm $algoritm;
	public visuotag $vt;
	public array $args = [ ];
	public string $path_object;
	# methods
	public function __construct ( algoritm $algoritm , array $args = [ ] ) {
		$this->algoritm = $algoritm;
		$this->args = $args;
	}
	
	public function args ( array $args ) {
		$this->args = $args;
	}
	public function algoritm ( ) {
	}
	public function release ( ) {
		$available = $this->available ?: [ '' ];
		$search = false;
		$default = true;
		$this->algoritm->include_ext( $this , $available , $search , $default );
	}
	
	public function default ( ) {
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
			$scheme->load( 'min' );
			
			$visuotag = $visuorelease->visuotag( );
			
			$error = null;
			$messages = null;
			$result = null;
			$form = null;

			$vars = $main->library->vars;

			$_form = $_POST[ 'form' ];
			$_formcheck = [
				'login'=>[ 'Логин' , $_form[ 'login' ] , 'string' , 5 , 20 ] ,
				'password'=>[ 'Пароль' , $_form[ 'password' ] , 'string' , 5 , 100 ] ,
				'remember'=>[ 'Запомнить' , $_form[ 'remember' ] , 'string' , 0 ] ,
			];
			
			$error = false;
			foreach( $_formcheck as $key=>$value ){
				$var_field = $value[ 0 ];
				$var_value = $value[ 1 ];
				$var_type = $value[ 2 ];
				$var_min = $value[ 3 ] ?: 0;
				$var_max = $value[ 4 ] ?: 1000;
				$var_check = $vars->check( $var_value  , $var_type , $var_min , $var_max );
				
				if( $var_type != $var_check ){
					$error = true;
					$main->messages->add( 'error' , 'Заполните поля правильно: ' . $var_field , 'Заполните поля правильно: [ ' . $var_field . ' ];' );
				}
			}
			
			# $main->messages->add( status: 'warning' , 'Вы ввели не верные учетные данные.' , 'Вы ввели не верные учетные данные.<br />' . $_formcheck[ 'remember' ][ 1 ] );

			# Работа с бд
			if( $error == false ){
				$db = $main->db->db( );
				$query = $db->query( );
				$query->select( 'select * from toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ $_formcheck[ 'login' ][ 1 ] , md5( $_formcheck[ 'password' ][ 1 ] ) ] , limit : true , limitstep : 1 );
				$results = $query->result( )[ 0 ];
				
				$main->messages->add( 'error' , 'Вы ввели не верные учетные данные.' , 'Вы ввели не верные учетные данные.<br />' . $_formcheck[ 'remember' ][ 1 ] );

				if( ! empty( $results ) ){
					$main->user->profile->login( $results , $_formcheck );
				} else{
					$error = true;
					$main->messages->add( 'error' , 'Вы ввели не верные учетные данные.' , 'Вы ввели не верные учетные данные.<br />' . $query->show( ) );
				}
			}
			

			if( $error == true ){
				$result = new visuotag( $visuorelease );
				$result->tag( 'div' )->content( 'ERROR!' );
			} else {
				if( isset( $_SESSION[ 'login' ][ 'redirect' ] ) ){
					$form_redirect = $_SESSION[ 'login' ][ 'redirect' ];
					unset( $_SESSION[ 'login' ][ 'redirect' ] );
				} else
					$form_redirect = $link->href( $url_domain , [ $url_base[ 0 ] , 'redirect' ] );

				$form[ 'reset' ] = false;
				$form[ 'redirect' ] = $form_redirect;
				$form[ 'redirect_time' ] = 3000;
				$main->messages->add( 'success' , 'Все хорошо.' , 'Вы будете автоматически перенаправлены.<br /><br /><a href="' . $form[ 'redirect' ] . '">Ручное перенаправление</a>' );
				# $result = $visuorelease->visuotag( )->release( );
				$result = new visuotag( $visuorelease );
				$result->tag( 'div' )->content( 'RESULT!' );
			}

			$messages = $main->messages->as_tag( ); // ->as_tag( )->root( )->release( );
			$min = true; 

			$release_json = [
				'error'=>$error ?: null ,
				'messages'=>$messages ?: null ,
				'result'=>$result ?: null ,
				'form'=>$form ?: null
			];

			$visuorelease->release_min( $error , $messages , $result , $form );

		} else {
			/*
			$vt_center = new visuotag( );

			if( $main->user->is_guest( ) )
				$vt_center->tag( 'div' )->content( 'Вам нужно авторизоваться' );
			else
				$vt_center->tag( 'div' )->content( 'Вы уже авторизованы' );
			
			$center->visuotag( )->append( $vt_center );

			*/
			# $scheme->load( 'error' );

			$this->algoritm->error_( $this );
		}
	}
	
}

?>