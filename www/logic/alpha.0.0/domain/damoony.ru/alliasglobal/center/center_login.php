<?PHP

namespace damoony_ru;

class alliasglobal_center_login {

	public \main $main;
	public alliasglobal_center $center;

	public bool $enabled = true;

	public function __construct ( alliasglobal_center $center ) {
		$this->main   = $center->main;
		$this->center = $center;
	}

	public function data ( ) : void {
		$this->center->wrapper_set( 'div' , [ 'class' => 'center' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'center/center_www - блок' );

		$this->center->section_add( 'test' , $vt->root( ) );
	}
}