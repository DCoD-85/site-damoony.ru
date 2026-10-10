<?PHP

namespace <domain_ns>\<allias>;

class algoritm_lang_<name>
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render( ); }
	public function logged ( ) { return $this->render( ); }

	private function render ( ) {
		$main = $this->main;
		$lang = $main->language;

		$main->alliasglobal->head->title(
			$lang->get( '<UPPER>_TITLE' , [ ] , '<name>' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , '<domain>' )
		);

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'visuo visuo-<name>' )
			->tag( 'h1' )->content( $lang->get( '<UPPER>_TITLE' , [ ] , '<name>' ) );

		$main->alliasglobal->center->visuotag( )->append( $vt );

		return true;
	}
}