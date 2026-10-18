<?PHP

namespace idoam_ru;

class alliasglobal_top_www {

	public \main $main;
	public alliasglobal_top $top;

	public bool $enabled = true;

	public function __construct ( alliasglobal_top $top ) {
		$this->main = $top->main;
		$this->top  = $top;
	}

	public function data ( ) : void {
		$this->top->wrapper_set( 'div' , [ 'class' => 'top' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'top/top_www - блок' );

		$this->top->section_add( 'test' , $vt->root( ) );
	}
}