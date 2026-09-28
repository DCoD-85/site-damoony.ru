<?PHP

namespace damoony;

class alliasglobal_top extends alliasblock {
	# --- state ---
	public string $alliasglobal_name = 'top';
	public bool   $settings_asmenu   = false;

	# --- methods ---
	protected function scheme  ( ) : void { }
	protected function data    ( ) : void { }
	protected function release ( ) : void { }
}