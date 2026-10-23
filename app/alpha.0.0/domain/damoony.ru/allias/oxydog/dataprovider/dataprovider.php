<?PHP

namespace damoony_ru\oxydog;

class dataprovider {
	# --- refs ---
	public \main $main;

	# --- providers ---
	public null|dataprovider_user  $user  = null;
	public null|dataprovider_doc   $doc   = null;
	public null|dataprovider_news  $news  = null;
	public null|dataprovider_files $files = null;
	public null|dataprovider_stats $stats = null;
	public null|dataprovider_audit $audit = null;

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
	}

	private function autoload ( ) : void {
		$base = __DIR__ . DS;

		( include_once $path = $base . 'dataprovider_base.php'  ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_user.php'  ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_doc.php'   ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_news.php'  ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_files.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_stats.php' ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_audit.php' ) or error_file( $path , __FILE__ , __LINE__ );

		$this->user  = new dataprovider_user( $this );
		$this->doc   = new dataprovider_doc( $this );
		$this->news  = new dataprovider_news( $this );
		$this->files = new dataprovider_files( $this );
		$this->stats = new dataprovider_stats( $this );
		$this->audit = new dataprovider_audit( $this );
	}
}