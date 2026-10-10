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

	public function __construct ( \main $main , array $args = [ ] ) {
		$this->main = $main;
		$this->args = $args;
		$this->algoritmdata = new \algoritmdata_( );
	}

	public function start ( ) {
		$main = $this->main;
		$scheme = $main->alliasscheme;
		$scheme->load( 'default' );
		$this->logic( );
		$this->release( );
		$scheme->reload( );
	}

	public function release ( ) {
		$this->algoritmdata->set( [ '([a-z]{2}-[a-z]{2})|lang' ] , false , true , true );
		$this->release_( $this );
	}

	public function default ( ) {
	}
}