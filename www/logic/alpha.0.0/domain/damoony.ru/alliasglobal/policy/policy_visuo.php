<?PHP

namespace damoony;

class alliasglobal_policy_visuo {

	public \main $main;
	public alliasglobal_policy $policy;

	public bool $enabled = false;

	public function __construct ( alliasglobal_policy $policy ) {
		$this->main   = $policy->main;
		$this->policy = $policy;
	}

	public function data ( ) : void {
		$this->policy->wrapper_set( 'div' , [ 'class' => 'policy' ] );

		$vt = new \visuotag( );
		$vt->tag( 'div' , class : 'test-block' )->content( 'policy/policy_visuo - блок' );

		$this->policy->section_add( 'test' , $vt->root( ) );
	}
}