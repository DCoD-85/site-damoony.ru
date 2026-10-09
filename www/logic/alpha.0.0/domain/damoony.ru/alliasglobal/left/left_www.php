<?PHP

namespace damoony_ru;

class alliasglobal_left_www {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_left $left;

	public function __construct ( alliasglobal_left $left ) {
		$this->main = $left->main;
		$this->left = $left;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'left_www.data.php';
		if ( is_file( $file ) ) include $file;
	}
}