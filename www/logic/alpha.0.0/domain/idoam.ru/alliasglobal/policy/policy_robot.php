<?PHP

namespace idoam_ru;

class alliasglobal_policy_robot {

	use alliasglobal_filler_trait;

	public \main $main;
	public alliasglobal_policy $policy;

	public function __construct ( alliasglobal_policy $policy ) {
		$this->main    = $policy->main;
		$this->policy = $policy;
	}

	public function data ( ) : void {
		$file = __DIR__ . DS . 'policy_robot.data.php';
		if ( is_file( $file ) ) include $file;
	}
}