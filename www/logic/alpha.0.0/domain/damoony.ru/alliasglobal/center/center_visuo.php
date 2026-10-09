<?PHP

namespace damoony;

class alliasglobal_center_visuo {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_center $center;

	public function __construct ( alliasglobal_center $center ) {
		$this->main   = $center->main;
		$this->center = $center;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'center_visuo.data.php';
		if ( is_file( $file ) ) include $file;
	}
}