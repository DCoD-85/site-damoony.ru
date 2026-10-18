<?PHP

namespace damoony_ru;

class alliasglobal_bottom_login {

	public \main $main;
	public alliasglobal_bottom $bottom;

	public bool $enabled = true;

	public function __construct ( alliasglobal_bottom $bottom ) {
		$this->main   = $bottom->main;
		$this->bottom = $bottom;
	}

	public function data ( ) : void {
		$this->bottom->wrapper_set( 'div' , [ 'class' => 'bottom' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'bottom/bottom_login - блок' );

		$this->bottom->section_add( 'test' , $vt->root( ) );
	}
}