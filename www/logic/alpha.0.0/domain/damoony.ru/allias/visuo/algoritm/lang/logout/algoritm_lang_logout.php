<?PHP

class algoritm_lang_logout {
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
		$this->available = [ 'index' , 'form' , 'login' , 'logout' , 'redirect' , 'development.admin.panel' ];
		$this->default = true;

		$this->algoritm->release_( $this );
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
		$scheme = $main->alliasglobal->scheme;
		
		if( $_POST ){
			$scheme->load( 'min' );
			
			$visuotag = $visuorelease->visuotag( );

			
			$dt_center = $visuotag->root( )->find( class : 'dt_center' );
			$dt_error = $visuotag->root( )->find( class : 'error' );
		

			$vt_center = new visuotag( );
			$vt_center->tag( 'div' )->content( 'INDEX ' . rand( 111111 , 999999 ) );
					
			! $dt_center ?: $dt_center->append( $vt_center );
			! $dt_error ?: $dt_error->tag( 'div' )->content( 'ERROR!' );
			

			$error = null;
			$messages = null;
			$result = null;
			$form = null;

			$vars = $main->library->vars;

			$_form = $_POST[ 'form' ];
			$_check = [
				'login'=>[ 'Логин' , $_POST[ 'form' ][ 'login' ] , 'string' , 5 , 20 ] ,
				'password'=>[ 'Пароль' , $_POST[ 'form' ][ 'password' ] , 'string' , 5 , 100 ] ,
				'remember'=>[ 'Запомнить' , $_POST[ 'form' ][ 'remember' ] , 'string' , 0 ] ,
			];
			
			$error = false;
			foreach( $_check as $key=>$value ){
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

			# Работа с бд
			if( $error == false ){
				$db = $main->db->db( );
				$db->open( 'login_damoony' );
				$query = $db->query( );
				$query->query( 'select * from toolsusers tu WHERE tu.login=%s AND tu.password=%s' , [ $_check[ 'login' ][ 1 ] , md5( $_check[ 'password' ][ 1 ] ) ] );
				$results = $query->result( );

				if( ! empty( $results[ 0 ] ) ) $main->user->profile->login( $results[ 0 ] );
			}
			

			if( $error == true ){
				$result = $_POST[ 'input_text_on' ] . ' | ' . '<div>ERROR</div>';
			} else {
				$form[ 'reset' ] = false;
				$form[ 'redirect' ] = 'https://development.ru.damoony2.ru/redirect';
				$form[ 'redirect_time' ] = 3000;
				$main->messages->add( 'success' , 'Все хорошо.' , 'Вы будете автоматически перенаправлены.<br /><br /><a href="' . $form[ 'redirect' ] . '">Ручное перенаправление</a>' );
				$messages = $main->messages->release( );
				$result = $visuorelease->visuotag( )->release( );
			}

			$messages = $main->messages->release( ); // ->as_tag( )->root( )->release( );
			
			$release_json = [
				'error'=>$error ?: null ,
				'messages'=>$messages ?: null ,
				'result'=>$result ?: null ,
				'resultTo'=>'form' ?: null ,
				'form'=>$form ?: null
			];

			$visuorelease->release_alternative_data( true , true , $release_json );

		} else {
			$main->user->profile->logout( );
			
			if( isset( $_SESSION[ 'login' ][ 'redirect' ] ) ){
				$form_redirect = $_SESSION[ 'login' ][ 'redirect' ];
				unset( $_SESSION[ 'login' ][ 'redirect' ] );
			} else
				$form_redirect = $link->href( $url_domain , [ 'index' ] );
			
			$vt_center = new visuotag( );
			$vt_center->tag( 'div' )->content( 'Вы вышли из учетной записи.' );

			header( 'Location: ' . $form_redirect );
		}

		$center->visuotag( )->append( $vt_center );
	}
	
}

?>