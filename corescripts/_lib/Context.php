<?PHP

class Context {

	private $onStep = null;

	public function __construct ( ?callable $onStep = null ) {
		$this->onStep = $onStep;
	}

	public function step ( string $name , bool $ok = true , string $msg = '' ) : void {
		if ( $this->onStep !== null ) {
			( $this->onStep )( $name , $ok , $msg );
		}
	}
}