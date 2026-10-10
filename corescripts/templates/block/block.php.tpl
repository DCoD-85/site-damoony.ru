<?PHP

namespace <domain_ns>;

class alliasglobal_<name> extends alliasblock {
	public string $alliasglobal_name = '<name>';

	# --- data ---
	protected function default_data ( ) : void {
		$file = __DIR__ . DS . '<name>.data.php';
		if ( is_file( $file ) ) include $file;
	}
}