<?PHP

namespace damoony\www;

class algoritm_lang_index
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	#   RELEASE
	# ---------------------------------------------------------------
	public function release ( ) {
		$default = $this->is_default( );

		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $default );

		$this->release_( $this );
	}

	# ---------------------------------------------------------------
	#   GUEST
	# ---------------------------------------------------------------
	public function guest ( ) {
		$main = $this->main;

		# ------------------------------------------------------
		#   POST → JSON min
		# ------------------------------------------------------
		if ( $this->is_post( ) ) {
			$struct = $main->visuostruct->min( );
			$struct->error( )->set( false );

			$name = (string) ( $_POST[ 'name' ] ?? '' );

			$messages = $main->messages;
			$messages->add( 'success' , 'OK' , 'Hello, ' . $name. '!' );
			$struct->messages( )->set( $messages->as_tag( ) );
			$struct->release( )->set( null );

			return true;
		}

		# ------------------------------------------------------
		#   GET → HTML через visuoel
		# ------------------------------------------------------
		$ve = new \visuoel( );

		# корневая форма
		$form = $ve->form( )->form( );
		$form->data( )->set(
			'test_form' ,             # name="test_form"
			'post' ,                  # method="post"
			'' ,                      # action — пусто = текущий URL
			form_jquery : true        # data-form-jquery + data-load
		);

		# поле name
		$field_name = new \visuoel_field_text( );
		$field_name->data( )->set( 'name' )->placeholder( 'Ваше имя' );
		$form->add( $field_name );

		# кнопка submit
		$btn = new \visuoel_field_submit( );
		$btn->data( )->text( 'Отправить' );
		$form->add( $btn );

		# вставка в центр страницы
		$main->alliasglobal->center->visuotag( )->el( $ve );

		return true;
	}

	# ---------------------------------------------------------------
	#   LOGGED
	# ---------------------------------------------------------------
	public function logged ( ) {
		$main = $this->main;
		$center = $main->alliasglobal->center;

		$vt = new \visuotag( );

		if ( $this->is_post( ) ) {
			$name = (string) ( $_POST[ 'name' ] ?? '' );

			$vt->tag( 'div' , class : 'result' )
				->content( 'Logged POST: ' . htmlspecialchars( $name , ENT_QUOTES ) );
		} else {
			$form = $vt->tag( 'form' , class : 'mini_form' );
			$form->attrs_add( 'method' , 'post' );

			$inp = $form->tag( 'input' );
			$inp->attrs_add( 'type' , 'text' );
			$inp->attrs_add( 'name' , 'name' );
			$inp->attrs_add( 'placeholder' , 'Ваше имя' );

			$btn = $form->tag( 'button' );
			$btn->attrs_add( 'type' , 'submit' );
			$btn->content( 'Отправить' );
		}

		$center->visuotag( )->append( $vt );

		return true;
	}
}

?>