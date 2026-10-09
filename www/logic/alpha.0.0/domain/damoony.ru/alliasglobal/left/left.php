<?PHP

namespace damoony;

class alliasglobal_left extends alliasblock {
	public string $alliasglobal_name = 'left';

	protected function default_data ( ) : void {
		$this->wrapper_set( 'div' , [ 'class' => 'left' ] );
	}
}