<?PHP

namespace damoony_ru\visuo;

class algoritm
	extends \algoritm_ {

	# path and classes
	public array $available = [ 'index' , '([a-z]{2}-[a-z]{2})|lang' , 'tests' ];
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

		# подмена $main->user на visuo-user
		$this->boot_user( );
	}

	# ---------------------------------------------------------------
	#   boot_user — заменить $main->user на visuo-версию и загрузить сессию.
	#   Идемпотентно: если уже visuo-user — просто boot().
	# ---------------------------------------------------------------
	private function boot_user ( ) : void {
		$main = $this->main;

		if ( ! ( $main->user instanceof \damoony_ru\visuo\user ) ) {
			$main->user = new \damoony_ru\visuo\user( $main );
		}
		$main->user->boot( );
	}

	public function start ( ) {
		$main = $this->main;

		$scheme = $main->alliasscheme;

		$scheme->load( 'visuo' );

		$this->logic( );
		$this->release( );

		$scheme->reload( );
	}

	public function logic ( ) {
		$main = $this->main;

		$referer = $_SERVER[ 'HTTP_REFERER' ] ?? '';
		$access_origin = '*';
		if ( $referer !== '' ) {
			$access_origin = substr( $referer , 0 , strlen( $referer ) - 1 );
		}

		header( 'Access-Control-Allow-Origin: ' . $access_origin );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Allow-Headers: Authorization, Origin, X-Requested-With, Accept, X-PINGOTHER, Content-Type' );
	}

	public function release ( ) {
		$this->algoritmdata->set( [ '([a-z]{2}-[a-z]{2})|lang' ] , false , true , true );
		$this->release_( $this );
	}

	public function default ( ) {
	}
}