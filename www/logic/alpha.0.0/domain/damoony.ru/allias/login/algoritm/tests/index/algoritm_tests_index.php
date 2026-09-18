<?PHP

namespace damoony\login;

class algoritm_tests_index
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'index';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$vt->tag( 'h2' )->content( 'Table of contents' );
		$vt->tag( 'p' )->content( 'A set of test pages showing the new algoritm_ framework.' );
		$vt->tag( 'ul' )
			->tag( 'li' )->content( 'basic - simplest render via guest()' )->parent( )
			->tag( 'li' )->content( 'roles - guest() vs logged()' )->parent( )
			->tag( 'li' )->content( 'helpers - is_ajax, is_guest, is_post, is_default' )->parent( )
			->tag( 'li' )->content( 'inherits - what is inherited from parent' )->parent( )
			->tag( 'li' )->content( 'error - guest() returns false to trigger 404' )->parent( )
			->tag( 'li' )->content( 'visuoelement - simple form via visuoelement' )->parent( );
		return $vt;
	}
}