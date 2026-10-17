<?PHP

namespace hert_online;

class alliasglobal_top_test {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_top $top;

	public function __construct ( alliasglobal_top $top ) {
		$this->main    = $top->main;
		$this->top = $top;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'top_test.data.php';
		if ( is_file( $file ) ) include $file;
	}
}