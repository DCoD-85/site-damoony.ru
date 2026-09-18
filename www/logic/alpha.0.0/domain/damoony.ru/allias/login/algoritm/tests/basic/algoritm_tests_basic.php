<?PHP

namespace damoony\login;

class algoritm_tests_basic
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'basic';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$vt->tag( 'h2' )->content( 'Basic test' );
		$vt->tag( 'p' )->content( 'This page overrides only page_content(). Everything else comes from parent.' );
		$vt->tag( 'ul' )
			->tag( 'li' )->content( 'Wrapper, header, navigation - from parent' )->parent( )
			->tag( 'li' )->content( 'Constructor - from parent (algoritm_)' )->parent( )
			->tag( 'li' )->content( 'Routing (guest/logged/ajax) - from parent' )->parent( );
		return $vt;
	}
}