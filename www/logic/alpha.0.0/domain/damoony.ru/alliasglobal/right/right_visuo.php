<?PHP

namespace damoony;

class alliasglobal_right_visuo {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_right $right;

	public function __construct ( alliasglobal_right $right ) {
		$this->main  = $right->main;
		$this->right = $right;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'right_visuo.data.php';
		if ( is_file( $file ) ) include $file;
	}
}