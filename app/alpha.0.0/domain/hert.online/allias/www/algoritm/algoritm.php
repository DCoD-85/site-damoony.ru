<?PHP

namespace hert_online\www;

class algoritm
	extends \algoritm_ {

	public array $available = [ 'index' , '([a-z]{2}-[a-z]{2})|lang' ];
	public string $basepath  = __DIR__ . DS;
	public string $baseclass = 'algoritm';
	public string $errorpath = __DIR__ . DS;
	public string $errorclass = 'algoritm_error';
	public string $authpath  = __DIR__ . DS;
	public string $authclass = 'algoritm_auth';

	public function start ( ) {
		$main = $this->main;
		$scheme = $main->alliasscheme;
		$scheme->load( 'default' );
		$this->logic( );
		$this->release( );
		$scheme->reload( );
	}

	

	public function default ( ) {
	}
}