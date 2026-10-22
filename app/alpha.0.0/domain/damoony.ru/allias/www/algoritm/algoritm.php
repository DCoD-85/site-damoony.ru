<?PHP

namespace damoony_ru\www;

class algoritm
	extends \algoritm_ {

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
		$this->load_data( );
	}

	public function start ( ) {
		$main = $this->main;
		$scheme = $main->alliasscheme;
		$scheme->load( 'www' );
		
		$this->logic( );
		$this->release( );
		$scheme->reload( );
	}

	public function default ( ) {
	}
}