<?PHP

namespace damoony;

class alliasglobal_right extends alliasblock {
	public string $alliasglobal_name = 'right';

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'right.data.php';
		if ( is_file( $file ) ) include $file;
	}
}