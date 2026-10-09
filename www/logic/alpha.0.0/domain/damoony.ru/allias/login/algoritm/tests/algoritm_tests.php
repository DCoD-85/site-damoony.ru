<?PHP

namespace damoony\login;

class algoritm_tests
	extends \algoritm_ {
	
	# --- page settings ---
	public array $available = [ '' , 'index' , 'basic' , 'roles' , 'helpers' , 'inherits' , 'error' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;
	
	# --- page name (override in child) ---
	protected string $page_name = 'index';
	
	# --- render: shared page wrapper ---
	public function guest () {
		$center = $this->main->alliasglobal->center;
		
		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-page' );
		$vt->tag( 'h1' )->content( 'Test: ' . $this->page_name );
		$vt->tag( 'div' , class : 'test-nav' )->append( $this->nav_html( ) );
		$vt->append( $this->page_content( ) );
		
		$center->visuotag( )->append( $vt->root( ) );
		
		return true;
	}
	
	# --- navigation: returns visuotag ---
	protected function nav_html ( ) {
		$links = [
			'index'        => 'Index' ,
			'basic'        => 'Basic' ,
			'roles'        => 'Roles' ,
			'helpers'      => 'Helpers' ,
			'inherits'     => 'Inherits' ,
			'error'        => 'Error' ,
		];
		
		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-nav-links' );
		
		$first = true;
		foreach ( $links as $key => $label ) {
			if ( ! $first ) {
				$vt->tag( 'span' , class : 'test-nav-sep' )->content( ' | ' )->parent( );
			}
			$first = false;
			
			$href = '/tests/' . $key;
			$cls = ( $key === $this->page_name ) ? 'active' : '';
			
			$vt->tag( 'a' , class : $cls )
				->attrs_add( 'href' , $href )
				->content( $label )
				->parent( );
		}
		
		return $vt;
	}
	
	# --- content (override in child) ---
	protected function page_content ( ) {
		$vt = new \visuotag( );
		$vt->tag( 'p' )->content( 'Base test page. Override page_content() in child.' );
		return $vt;
	}
}

?>