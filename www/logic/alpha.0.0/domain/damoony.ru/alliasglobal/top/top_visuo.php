<?PHP

namespace damoony;

class alliasglobal_top_visuo {

	public \main $main;
	public alliasglobal_top $top;

	public bool $enabled = false;

	public function __construct ( alliasglobal_top $top ) {
		$this->main = $top->main;
		$this->top  = $top;
	}

	public function data ( ) : void {
		$this->top->wrapper_set( 'div' , [ 'class' => 'top' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'top/top_visuo - блок' );

		$this->top->section_add( 'test' , $vt->root( ) );
	}
}