<?PHP

class core_time {
	public core $core;
	public \main $main;
	
	public float $start_mt;
	public int   $start_hr;
	
	public function __construct ( core $core ) {
		$this->core = $core;
		$this->main = $core->main;
		$this->start_mt = microtime( true );
		$this->start_hr = hrtime( true );
	}
	
	public function elapsed_ms () : float {
		return ( microtime( true ) - $this->start_mt ) * 1000;
	}
	
	public function elapsed_hr_ms () : float {
		return ( hrtime( true ) - $this->start_hr ) / 1000000;
	}
	
	public function reset ( ) : void {
		$this->start_mt = microtime( true );
		$this->start_hr = hrtime( true );
	}
}