<?PHP

namespace damoony\login;

class algoritm_tests_inherits
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'inherits';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$vt->tag( 'h2' )->content( 'Inheritance' );
		$vt->tag( 'p' )->content( 'This child has no constructor, no args(), no algoritm(). All inherited.' );
		
		$vt->tag( 'h3' )->content( 'Chain:' );
		$vt->tag( 'ul' )
			->tag( 'li' )->content( 'algoritm_ - base framework (constructor, helpers, routing)' )->parent( )
			->tag( 'li' )->content( 'algoritm_tests - /tests parent (navigation, wrapper)' )->parent( )
			->tag( 'li' )->content( 'algoritm_tests_inherits - this page (only page_content)' )->parent( );
		
		$vt->tag( 'h3' )->content( 'Class names:' );
		$vt->tag( 'p' )->content( 'Current class: ' . get_class( $this ) );
		$vt->tag( 'p' )->content( 'Parent class: ' . get_parent_class( $this ) );
		$vt->tag( 'p' )->content( 'Grandparent: ' . get_parent_class( get_parent_class( $this ) ) );
		
		return $vt;
	}
}