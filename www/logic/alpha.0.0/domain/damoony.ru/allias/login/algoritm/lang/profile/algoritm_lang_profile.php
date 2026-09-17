<?PHP

class algoritm_lang_profile {
	# vars
	public algoritm $algoritm;
	public visuotag $vt;
	public array $args = [ ];
	public string $path_object;
	# public array $available = [ ];
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
		$this->available = [ 'index' , 'edit' , 'remove' ];
		$this->search = true;
		$this->default = true;

		$this->algoritm->release_( $this );
	}
	
	public function default ( ) {
		$main = $this->algoritm->main;
		$visuorelease = $main->visuorelease;
		$lang = $main->language;
		
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
		
		
		$vt_center = new visuotag( );
		
		$content_login_guest_welcome = $lang->get( 'LOGIN_GUEST_WELCOME' , [ 'profile' , $main->user->profile->data[ 'login' ] ] , 'Это приветственное сообщение профиля.' );
		$content_login_guest_need_auth = $lang->get( 'LOGIN_GUEST_NEED_AUTH' , [ ] , ' Нужно авторизоваться.' );
		$content_login_guest_also_auth = $lang->get( 'LOGIN_GUEST_ALSO_AUTH' , [ ] , ' Вы уже авторизованы.' );
		$vt_center = $vt_center->tag( 'div' );
		$vt_center->content( $content_login_guest_welcome );

		$is_guest = $main->user->is_guest( );

		if( $is_guest ) $vt_center->content( $content_login_guest_need_auth );
		else if( ! $is_guest ) $vt_center->content( $content_login_guest_also_auth );

		$vt_center->parent( )->tag( 'div' )->content( ' Как то так.' );
		$center->visuotag( )->append( $vt_center );
		# $href = $link->href( $url_domain , [ $url_base[ 0 ] , 'form' ] );
		# header( 'Location: ' . $href );
	}
	
}

?>