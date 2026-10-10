<?PHP

namespace <domain_ns>;

class alliasglobal_<block>_<name> {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_<block> $<block>;

	public function __construct ( alliasglobal_<block> $<block> ) {
		$this->main    = $<block>->main;
		$this-><block> = $<block>;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . '<block>_<name>.data.php';
		if ( is_file( $file ) ) include $file;
	}
}