<?PHP

namespace damoony;

abstract class alliasblock {
	# --- refs ---
	public alliasglobal $alliasglobal;
	public \main $main;
	public \visuorelease $visuorelease;

	# --- state ---
	public \visuotag $visuotag;
	public null|\visuotag $visuotag_title = null;
	public string $alliasglobal_name = '';
	public string $allias_accepted = '';
	public bool $settings_on = false;
	public bool $settings_asmenu = true;

	public null|alliasglobal_menu $alliasglobal_menu = null;

	# --- methods ---
	public function __construct ( alliasglobal $alliasglobal ) {
		$this->alliasglobal    = $alliasglobal;
		$this->main            = $alliasglobal->main;
		$this->visuorelease    = $this->main->visuorelease;
		$this->visuotag        = new \visuotag( $this->visuorelease );
		$this->allias_accepted = $this->main->main_settings->allias_accepted( );

		$this->init( );
	}

	protected function init ( ) : void {
		$localname      = $this->alliasglobal_name;
		$alliaslocal_on = $this->main->alliaslocal->{$localname}->settings_on ?? false;

		if ( $alliaslocal_on === true ) {
			$this->visuotag->clear( )->append( $this->main->alliaslocal->{$localname}->visuotag );
		} else {
			if ( $this->alliasglobal_menu !== null ) $this->alliasglobal_menu->clear( );
			$this->algoritm( );
		}
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