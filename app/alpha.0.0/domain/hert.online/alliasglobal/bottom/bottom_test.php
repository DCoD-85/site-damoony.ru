<?PHP

namespace hert_online;

class alliasglobal_bottom_test {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_bottom $bottom;

	public function __construct ( alliasglobal_bottom $bottom ) {
		$this->main    = $bottom->main;
		$this->bottom = $bottom;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'bottom_test.data.php';
		if ( is_file( $file ) ) include $file;
	}
}