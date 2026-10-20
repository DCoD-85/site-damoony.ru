<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_profile
	extends \algoritm_ {

	public array $timezone_options = [ ];
	public array $lang_options     = [ ];

	# ---------------------------------------------------------------
	

	public function guest  ( ) { return false; }   # auth=true — гость сюда не дойдёт
	public function logged ( ) { return $this->render( ); }

	# ---------------------------------------------------------------
	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;
		$user = $main->user;

		$ok = false;

		# --- POST ---
		if ( $this->is_post( ) ) {
			if ( ! csrf_check( ) ) {
				# ошибка CSRF — просто игнорим смену, показываем форму
			} else {
				$new_tz   = (string) ( $_POST[ 'timezone' ] ?? '' );
				$new_lang = (string) ( $_POST[ 'lang' ]     ?? '' );

				$tz_ok   = $user->update_timezone( $new_tz );
				$lang_ok = $user->update_lang( $new_lang );

				if ( $tz_ok || $lang_ok ) {
					$ok = true;
				}
			}
		}

		# --- title ---
		$main->alliasglobal->head->title(
			$lang->get( 'PROFILE_TITLE' , [ ] , 'Profile' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		# --- UI ---
		$ve = new \visuoel( );

		if ( $ok ) {
			$ve->message( )->success( )->data( )->set(
				$lang->get( 'PROFILE_SAVED' , [ ] , 'Profile saved' )
			);
		}

		$data = $user->data( );

		# --- info block ---
		$table = $ve->table( );

		$this->row_kv( $table , $lang->get( 'PROFILE_LOGIN' , [ ] , 'Login' ) ,       (string) ( $data[ 'login' ]        ?? '' ) );
		$this->row_kv( $table , $lang->get( 'PROFILE_EMAIL' , [ ] , 'Email' ) ,       (string) ( $data[ 'email' ]        ?? '' ) );
		$this->row_kv( $table , $lang->get( 'PROFILE_DISPLAY_NAME' , [ ] , 'Name' ) , (string) ( $data[ 'display_name' ] ?? '' ) );
		$this->row_kv( $table , $lang->get( 'PROFILE_CREATED_AT' , [ ] , 'Registered' ) , (string) ( $data[ 'created_at' ] ?? '' ) );
		$this->row_kv( $table , $lang->get( 'PROFILE_LAST_LOGIN' , [ ] , 'Last login' ) , (string) ( $data[ 'last_login_at' ] ?? '' ) );

		# --- settings form ---
		$form = $ve->form( )->form( );
		$form->data( )->set(
			'visuo_profile_form' ,
			'post' ,
			'' ,
			'application/x-www-form-urlencoded' ,
			false ,
			$lang->get( 'PROFILE_TITLE' , [ ] , 'Profile' ) ,
			'visuo-form visuo-form-profile'
		);

		# CSRF
		$csrf = new \visuoel_input_hidden( );
		$csrf->data( )->set( '_csrf' , csrf_token( ) );
		$form->add( $csrf );

		# timezone
		$tz_opts = $this->timezone_options( );
		$f_tz = $ve->field( )->select( );
		$f_tz->data( )
			->set( 'timezone' )
			->label( $lang->get( 'PROFILE_TIMEZONE' , [ ] , 'Timezone' ) )
			->options( $tz_opts )
			->selected( (string) ( $data[ 'timezone' ] ?? 'Europe/Moscow' ) );
		$form->add( $f_tz );

		# lang
		$lang_opts = [
			'ru-ru' => $lang->get( 'LANG_RU' , [ ] , 'Russian' ) ,
			'en-en' => $lang->get( 'LANG_EN' , [ ] , 'English' ) ,
			'ch-ch' => $lang->get( 'LANG_CH' , [ ] , 'Chinese' ) ,
		];
		$f_lang = $ve->field( )->select( );
		$f_lang->data( )
			->set( 'lang' )
			->label( $lang->get( 'PROFILE_LANG' , [ ] , 'Language' ) )
			->options( $lang_opts )
			->selected( (string) ( $data[ 'lang' ] ?? 'ru-ru' ) );
		$form->add( $f_lang );

		# submit
		$f_btn = new \visuoel_field_submit( );
		$f_btn->data( )->text( $lang->get( 'BTN_SAVE' , [ ] , 'Save' ) );
		$form->add( $f_btn );

		$main->alliasglobal->center->visuotag( )->el( $ve );

		return true;
	}

	# ---------------------------------------------------------------
	private function row_kv ( \visuoel_table $table , string $key , string $value ) {
		$row = $table->row( );
		$row->cell( 30 )->set( $key );
		$row->cell( 70 )->set( $value );
	}

	# ---------------------------------------------------------------
	private function timezone_options ( ) : array {
		$out = [ ];
		foreach ( timezone_identifiers_list( ) as $tz ) {
			$out[ $tz ] = $tz;
		}
		return $out;
	}
}