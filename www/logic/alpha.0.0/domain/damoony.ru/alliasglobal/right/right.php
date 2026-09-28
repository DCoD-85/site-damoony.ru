<?PHP

namespace damoony;

class alliasglobal_right extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'right';
	public bool   $settings_asmenu   = false;

	# --- methods ---
	protected function scheme  ( ) : void { }
	protected function data    ( ) : void { }
	protected function release ( ) : void { }
}