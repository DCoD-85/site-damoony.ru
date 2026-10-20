<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_login
	extends \algoritm_ {

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		return $this->release_( $this );
	}

	# ---------------------------------------------------------------
	public function guest  ( ) { return $this->render( false ); }
	public function logged ( ) { return $this->redirect_profile( ); }

	# --- AJAX ---
	public function ajax_guest  ( ) { 
		$main = $this->main;
		$lang = $main->language;
		$user = $main->user;

		$errors = [ ];
		$post_login = '';

		# --- POST ---
		if ( $this->is_post( ) ) {
			if ( ! csrf_check( ) ) {
				$errors[ ] = $lang->get( 'ERROR_CSRF' , [ ] , 'Security error' );
			} else {
				$post_login = trim( (string) ( $_POST[ 'login' ] ?? '' ) );
				$password   = (string) ( $_POST[ 'password' ] ?? '' );
				$remember   = ! empty( $_POST[ 'remember' ] );

				if ( $post_login === '' || $password === '' ) {
					$errors[ ] = $lang->get( 'ERROR_LOGIN_FAILED' , [ ] , 'Invalid login or password' );
				} elseif ( $user->authorize( $post_login , $password , $remember ) ) {
					# Успешный вход — командуем JS сделать редирект
					$min = $main->visuostruct->min( );
					$min->form( )->set( false , '/ru-ru/profile' );
					return;
				} else {
					$errors[ ] = $lang->get( 'ERROR_LOGIN_FAILED' , [ ] , 'Invalid login or password' );
				}
			}
		}

				# --- Если есть ошибки — отдаем их в messages ---
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

		# Если просто открыли страницу через AJAX (GET)
		$this->render( true );
	}

	public function ajax_logged ( ) { 
		return $this->redirect_profile( ); 
	}

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

		$errors    = [ ];
		$post_login = '';

		# --- title ---
		$main->alliasglobal->head->title(
			$lang->get( 'PAGE_LOGIN' , [ ] , 'Login' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
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
			'visuo_login_form' ,
			'post' ,
			'' ,
			'application/x-www-form-urlencoded' ,
			true , // Включаем AJAX
			$lang->get( 'PAGE_LOGIN' , [ ] , 'Login' ) ,
			'visuo-form visuo-form-login'
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
			->required( )
			->autocomplete( 'username' );
		$form->add( $f_login );

		# password
		$f_pass = $ve->field( )->password( );
		$f_pass->data( )
			->set( 'password' )
			->label( $lang->get( 'FORM_PASSWORD_LABEL' , [ ] , 'Password' ) )
			->required( )
			->autocomplete( 'current-password' );
		$form->add( $f_pass );

		# remember
		$f_rem = $ve->field( )->checkbox( );
		$f_rem->data( )
			->set( 'remember' , '1' )
			->label( $lang->get( 'FORM_REMEMBER_ME' , [ ] , 'Remember me' ) )
			->checked( ! empty( $_POST[ 'remember' ] ) );
		$form->add( $f_rem );

		# submit
		$f_btn = new \visuoel_field_submit( );
		$f_btn->data( )->text( $lang->get( 'FORM_SUBMIT_LOGIN' , [ ] , 'Sign in' ) );
		$form->add( $f_btn );

		# ссылка на регистрацию
		$library  = $main->library;
		$link     = $library->link( );
		$url      = $library->url( );
		$url_base = $url->static_base;
		$reg_href = $link->href( false , [ $url_base[ 0 ] , 'registration' ] , use_all_adress_data : false );

		$reg_block = $ve->block( )->html( );
		$reg_block->data( )->set(
			'<p class="visuo-form-link"><a href="' . h( $reg_href ) . '">'
			. h( $lang->get( 'MENU_REGISTRATION' , [ ] , 'Registration' ) )
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