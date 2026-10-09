<?PHP

namespace <domain_ns>;

class alliasglobal_script_head extends alliasglobalscript {
	# --- state ---
	public string $script_name = 'head';

	# --- custom ---
	public string      $title = '';
	public array       $css   = [ ];
	public array       $js    = [ ];
	public null|string $lang  = null;

	# --- methods ---
	public function title ( string $title ) : static {
		$this->title = $title;
		return $this;
	}

	public function css ( string $href ) : static {
		$this->css[ ] = $href;
		return $this;
	}

	public function js ( string $src ) : static {
		$this->js[ ] = $src;
		return $this;
	}

	public function lang ( string $lang ) : static {
		$this->lang = $lang;
		return $this;
	}

	public function run ( ) : void {
		if ( $this->run_done ) return;
		$this->run_done = true;

		$meta = $this->main->visuorelease->meta( );
		if ( $meta === null ) return;

		if ( $this->title !== '' ) {
			$meta->title( )->data( $this->title );
		}

		foreach ( $this->css as $href ) {
			$meta->link( )->css( $href );
		}

		foreach ( $this->js as $src ) {
			$meta->link( )->js( $src );
		}
	}
}