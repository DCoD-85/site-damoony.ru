<?PHP

namespace damoony;

class alliasglobal_right extends alliasblock {
	public string $alliasglobal_name = 'right';

	protected function default_data ( ) : void {
		$this->wrapper_set( 'div' , [ 'class' => 'right' ] );
	}
}