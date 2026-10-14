<?PHP

namespace hert_online;

class alliasglobal_debug_test {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_debug $debug;

	public function __construct ( alliasglobal_debug $debug ) {
		$this->main    = $debug->main;
		$this->debug = $debug;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'debug_test.data.php';
		if ( is_file( $file ) ) include $file;
	}
}