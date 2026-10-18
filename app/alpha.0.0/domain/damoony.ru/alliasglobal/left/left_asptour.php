<?PHP

namespace damoony_ru;

class alliasglobal_left_asptour {

	public \main $main;
	public alliasglobal_left $left;

	public bool $enabled = true;

	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
	}

	public function data ( ) : void {
		$this->left->wrapper_set( 'div' , [ 'class' => 'left' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'left/left_asptour - блок' );

		$this->left->section_add( 'test' , $vt->root( ) );
	}
}