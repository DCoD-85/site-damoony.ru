<?PHP

namespace damoony;

class alliasglobal_center extends alliasblock {
	public string $alliasglobal_name = 'center';
	public bool   $settings_asmenu   = false;

	protected function default_data ( ) : void {
		$file = __DIR__ . DS . 'center.data.php';
		if ( is_file( $file ) ) include $file;
	}
}