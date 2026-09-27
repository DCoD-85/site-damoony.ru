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
		$center = $main->alliasglobal->center;

		$vt = new \visuotag( );

		if ( $this->is_post( ) ) {
			$name = (string) ( $_POST[ 'name' ] ?? '' );

			$vt->tag( 'div' , class : 'result' )
				->content( 'Guest POST: ' . htmlspecialchars( $name , ENT_QUOTES ) );
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