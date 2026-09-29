<?PHP

namespace damoony\www;

class dataprovider {
	# --- refs ---
	public \main $main;

	# --- providers ---
	public null|dataprovider_news     $news     = null;
	public null|dataprovider_feedback $feedback = null;

	# --- methods ---
	public function __construct ( \main $main ) {
		$this->main = $main;
		$this->autoload( );
	}

	private function autoload ( ) : void {
		$base = __DIR__ . DS;

		( include_once $path = $base . 'dataprovider_base.php'     ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_news.php'     ) or error_file( $path , __FILE__ , __LINE__ );
		( include_once $path = $base . 'dataprovider_feedback.php' ) or error_file( $path , __FILE__ , __LINE__ );

		$this->news     = new dataprovider_news( $this );
		$this->feedback = new dataprovider_feedback( $this );
	}
}