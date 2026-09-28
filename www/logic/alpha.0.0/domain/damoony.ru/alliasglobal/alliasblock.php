<?PHP

namespace damoony;

abstract class alliasblock {
	# --- refs ---
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;

	# --- state ---
	public \visuotag $visuotag;
	public string $alliasglobal_name = '';
	public string $allias_accepted   = '';
	public bool   $settings_on       = false;
	public bool   $settings_asmenu   = true;

	public null|alliasglobal_menu $menu = null;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal    = $alliasglobal;
		$this->main            = $alliasglobal->main;
		$this->visuorelease    = $this->main->visuorelease;
		$this->visuotag        = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );
		$this->menu            = new alliasglobal_menu( $alliasglobal , $this->alliasglobal_name );

		$this->init( );
	}

	protected function init ( ) : void {
		$this->algoritm( );
	}

	public function algoritm ( ) : void {
		$this->scheme( );
		$this->data( );
		$this->release( );
	}

	abstract protected function scheme  ( ) : void;
	abstract protected function data    ( ) : void;
	abstract protected function release ( ) : void;

	public function reload ( ) : void {
		$this->visuotag->clear( );
		$this->scheme( );
		$this->release( );
	}

	public function as_tag ( ) : \visuotag {
		return $this->visuotag->root( );
	}

	public function visuotag ( ) : \visuotag {
		return $this->visuotag;
	}

	public function asmenu ( bool $asmenu ) : void {
		$this->settings_asmenu = $asmenu;
		if ( $asmenu === false ) $this->visuotag->clear( );
	}
}