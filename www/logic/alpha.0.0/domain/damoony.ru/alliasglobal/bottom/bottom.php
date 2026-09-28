<?PHP

namespace damoony;

class alliasglobal_bottom extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'bottom';
	public bool   $settings_asmenu   = false;

	# --- methods ---
	protected function scheme  ( ) : void { }
	protected function data    ( ) : void { }
	protected function release ( ) : void { }
}