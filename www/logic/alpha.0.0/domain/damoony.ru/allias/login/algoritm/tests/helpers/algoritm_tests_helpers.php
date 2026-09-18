<?PHP

namespace damoony\login;

class algoritm_tests_helpers
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'helpers';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		
		$vt->tag( 'h2' )->content( 'Helpers' );
		$vt->tag( 'p' )->content( 'Four helper methods from the parent class algoritm_.' );
		
		$table = $vt->tag( 'table' , class : 'visuotable' );
		$table->tag( 'tr' )
			->tag( 'td' )->content( 'is_ajax()' )->parent( )
			->tag( 'td' )->content( $this->is_ajax( ) ? 'true' : 'false' )->parent( );
		$table->tag( 'tr' )
			->tag( 'td' )->content( 'is_guest()' )->parent( )
			->tag( 'td' )->content( $this->is_guest( ) ? 'true' : 'false' )->parent( );
		$table->tag( 'tr' )
			->tag( 'td' )->content( 'is_post()' )->parent( )
			->tag( 'td' )->content( $this->is_post( ) ? 'true' : 'false' )->parent( );
		$table->tag( 'tr' )
			->tag( 'td' )->content( 'is_default()' )->parent( )
			->tag( 'td' )->content( $this->is_default( ) ? 'true' : 'false' )->parent( );
		
		return $vt;
	}
}