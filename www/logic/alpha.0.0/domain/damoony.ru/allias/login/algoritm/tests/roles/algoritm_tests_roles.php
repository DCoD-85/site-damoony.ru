<?PHP

namespace damoony\login;

class algoritm_tests_roles
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'roles';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$is_guest = $this->is_guest( );
		
		$vt->tag( 'h2' )->content( 'Roles' );
		$vt->tag( 'p' )->content( 'This page shows the current user role.' );
		
		$vt->tag( 'h3' )->content( 'Current user:' );
		$vt->tag( 'p' )->content( $is_guest ? 'Guest' : 'Authorized user' );
		
		$vt->tag( 'h3' )->content( 'How it works:' );
		$vt->tag( 'ul' )
			->tag( 'li' )->content( 'default() -> role() -> guest() OR logged()' )->parent( )
			->tag( 'li' )->content( 'guest() - shown to guest' )->parent( )
			->tag( 'li' )->content( 'logged() - shown to authorized' )->parent( );
		
		return $vt;
	}
}