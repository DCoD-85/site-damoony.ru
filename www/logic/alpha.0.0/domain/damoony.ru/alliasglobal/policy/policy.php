<?PHP

namespace damoony;

class alliasglobal_policy extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'policy';
	public bool   $settings_asmenu   = false;

	# --- custom ---
	public string $cookie_name = 'policy_agreement';
	public string $title       = 'Пользовательское соглашение';
	public string $text        = 'Продолжая пользоваться сайтом, вы соглашаетесь с условиями пользовательского соглашения и обработкой персональных данных.';
	public string $link_href   = '/policy';
	public string $link_text   = 'Полный текст';

	# --- methods ---
	public function algoritm ( ) : void {
		if ( ! $this->should_show( ) ) return;
		parent::algoritm( );
	}

	protected function should_show ( ) : bool {
		if ( ( $_COOKIE[ $this->cookie_name ] ?? '' ) === 'accept' ) return false;
		if ( $this->main->is_guest( ) === false ) return false;
		return true;
	}

	protected function scheme ( ) : void {
		$this->visuotag
			->tag( 'div' , class : 'policy_fon' )
				->tag( 'div' , class : 'policy_body' )
					->tag( 'div' , class : 'policy_title' )->content( $this->title )->parent( )
					->tag( 'div' , class : 'policy_text'  )->content( $this->text  )->parent( )
					->tag( 'div' , class : 'policy_actions' );
	}

	protected function data ( ) : void {
	}

	protected function release ( ) : void {
		$slot = $this->visuotag->root( )->find( class : 'policy_actions' );
		if ( $slot === null ) return;

		$slot->tag( 'a' )
			->attrs_add( 'href' , $this->link_href )
			->content( $this->link_text );
	}
}