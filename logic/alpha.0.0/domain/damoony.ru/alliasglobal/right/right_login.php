<?PHP

namespace damoony_ru;

class alliasglobal_right_login {

	public \main $main;
	public alliasglobal_right $right;

	public bool $enabled = true;

	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
	}

	public function data ( ) : void {
		$this->right->wrapper_set( 'div' , [ 'class' => 'right' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'right/right_login - блок' );

		$this->right->section_add( 'test' , $vt->root( ) );
	}
}