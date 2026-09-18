<?PHP

namespace damoony\login;

class algoritm_tests_error
	extends algoritm_tests {
	
	public array $available = [ '' ];
	protected string $page_name = 'error';
	
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$vt->tag( 'h2' )->content( 'Error test' );
		$vt->tag( 'p' )->content( 'This page always returns false from guest(). It should trigger the error page.' );
		$vt->tag( 'p' )->content( 'If you see this page - the error mechanism did NOT trigger.' );
		return $vt;
	}
	
	public function guest ( ) {
		# return false -> parent should call error_()
		return false;
	}
}