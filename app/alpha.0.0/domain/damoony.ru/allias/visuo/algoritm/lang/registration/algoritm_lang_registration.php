<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_registration
	extends \algoritm_ {

	public array $available = [ 'registration' ];
	public bool  $auth      = false;
	public bool  $search    = false;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	

	# ---------------------------------------------------------------
	public function guest  ( ) { return $this->render( false ); }
	public function logged ( ) { return $this->redirect_profile( ); }

	# --- AJAX ---
	public function ajax_guest  ( ) { 
		$main = $this->main;
		$lang = $main->language;
		$user = $main->user;

		$errors = [ ];
		$ok     = false;

		# --- POST ---
		if ( $this->is_post( ) ) {
			if ( ! csrf_check( ) ) {
				$errors[ ] = $lang->get( 'ERROR_CSRF' , [ ] , 'Security error' );
			} else {
				$post_login = trim( (string) ( $_POST[ 'login' ] ?? '' ) );
				$post_email = trim( (string) ( $_POST[ 'email' ] ?? '' ) );
				$post_disp  = trim( (string) ( $_POST[ 'display_name' ] ?? '' ) );

				$res = $user->register( [
					'login'            => $post_login ,
					'email'            => $post_email ,
					'display_name'     => $post_disp ,
					'password'         => (string) ( $_POST[ 'password' ] ?? '' ) ,
					'password_confirm' => (string) ( $_POST[ 'password_confirm' ] ?? '' ) ,
				] );

				if ( $res[ 'error' ] === null ) {
					$ok = true;
				} else {
					$errors[ ] = $lang->get( $res[ 'error' ] , [ ] , $res[ 'error' ] );
				}
			}
		}

		# --- Если успех ---
		if ( $ok ) {
			$msg_vt = new \visuotag( );
			$msg_vt->tag( 'div' , class : 'message success' )->content( $lang->get( 'OK_REGISTER' , [ ] , 'Registration successful. Please sign in.' ) )->parent( );
			
			$min = $main->visuostruct->min( );
			$min->messages( )->set( $msg_vt );
			$min->form( )->set( false , '/ru-ru/login' ); // редирект на логин
			return;
		}

		# --- Если ошибки ---
		if ( ! empty( $errors ) ) {
			$msg_vt = new \visuotag( );
			foreach ( $errors as $e ) {
				$msg_vt->tag( 'div' , class : 'message error' )->content( $e )->parent( );
			}
			
			$min = $main->visuostruct->min( );
			$min->error( )->set( true );
			$min->messages( )->set( $msg_vt );
			$min->form( )->set( true , null ); // reset = true
			return;
		}

		# Если просто открыли через AJAX
		$this->render( true );
	}

	public function ajax_logged ( ) { return $this->redirect_profile( ); }

	# ---------------------------------------------------------------
	private function redirect_profile ( ) {
		$main = $this->main;
		$library = $main->library;
		$link = $library->link( );
		$url  = $library->url( );
		$url_base = $url->static_base;

		$href = $link->href( false , [ $url_base[ 0 ] , 'profile' ] , use_all_adress_data : false );
		redirect( $href );
	}

	# ---------------------------------------------------------------
	private function render ( bool $is_ajax = false ) {
		$main = $this->main;
		$lang = $main->language;
		$user = $main->user;

		$errors     = [ ];
		$post_login = '';
		$post_email = '';
		$post_disp  = '';

		# --- title ---
		$main->alliasglobal->head->title(
			$lang->get( 'PAGE_REGISTRATION' , [ ] , 'Registration' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		# --- UI ---
		$ve = new \visuoel( );

		# ошибки
		foreach ( $errors as $e ) {
			$ve->message( )->error( )->data( )->set( $e );
		}

		# форма
		$form = $ve->form( )->form( );
		$form->data( )->set(
			'visuo_register_form' ,
			'post' ,
			'' ,
			'application/x-www-form-urlencoded' ,
			true , // Включаем AJAX
			$lang->get( 'PAGE_REGISTRATION' , [ ] , 'Registration' ) ,
			'visuo-form visuo-form-registration'
		);

		# CSRF
		$csrf = new \visuoel_input_hidden( );
		$csrf->data( )->set( '_csrf' , csrf_token( ) );
		$form->add( $csrf );

		# login
		$f_login = $ve->field( )->text( );
		$f_login->data( )
			->set( 'login' , $post_login )
			->label( $lang->get( 'FORM_LOGIN_LABEL' , [ ] , 'Login' ) )
			->hint( $lang->get( 'ERROR_REGISTER_LOGIN_FORMAT' , [ ] , 'Login: 3–32 chars' ) )
			->required( )
			->autocomplete( 'username' );
		$form->add( $f_login );

		# email
		$f_email = $ve->field( )->email( );
		$f_email->data( )
			->set( 'email' , $post_email )
			->label( $lang->get( 'FORM_EMAIL_LABEL' , [ ] , 'Email' ) )
			->required( )
			->autocomplete( 'email' );
		$form->add( $f_email );

		# display_name
		$f_disp = $ve->field( )->text( );
		$f_disp->data( )
			->set( 'display_name' , $post_disp )
			->label( $lang->get( 'FORM_DISPLAY_NAME_LABEL' , [ ] , 'Display name' ) )
			->autocomplete( 'nickname' );
		$form->add( $f_disp );

		# password
		$f_pass = $ve->field( )->password( );
		$f_pass->data( )
			->set( 'password' )
			->label( $lang->get( 'FORM_PASSWORD_LABEL' , [ ] , 'Password' ) )
			->hint( $lang->get( 'ERROR_REGISTER_PASSWORD_SHORT' , [ ] , 'Min 8 chars' ) )
			->required( )
			->autocomplete( 'new-password' );
		$form->add( $f_pass );

		# password_confirm
		$f_pass2 = $ve->field( )->password( );
		$f_pass2->data( )
			->set( 'password_confirm' )
			->label( $lang->get( 'FORM_PASSWORD_CONFIRM_LABEL' , [ ] , 'Confirm password' ) )
			->required( )
			->autocomplete( 'new-password' );
		$form->add( $f_pass2 );

		# submit
		$f_btn = new \visuoel_field_submit( );
		$f_btn->data( )->text( $lang->get( 'FORM_SUBMIT_REGISTER' , [ ] , 'Sign up' ) );
		$form->add( $f_btn );

		# ссылка на вход
		$library  = $main->library;
		$link     = $library->link( );
		$url      = $library->url( );
		$url_base = $url->static_base;
		$login_href = $link->href( false , [ $url_base[ 0 ] , 'login' ] , use_all_adress_data : false );

		$ve->block( )->html( )->data( )->set(
			'<p class="visuo-form-link"><a href="' . h( $login_href ) . '">'
			. h( $lang->get( 'MENU_LOGIN' , [ ] , 'Login' ) )
			. '</a></p>'
		);

		# --- Вывод (HTML или AJAX JSON) ---
		if ( $is_ajax ) {
			$main->visuostruct->ajax( )->address( )->set( $_SERVER['REQUEST_URI'] );
			$main->visuostruct->ajax( )->center( )->set( $ve->visuotag( ) );
		} else {
			$main->alliasglobal->center->visuotag( )->el( $ve );
		}

		return true;
	}
}