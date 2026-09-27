<?PHP

class visuostruct {
	# --- refs ---
	public null|\main $main = null;

	# --- state ---
	public null|visuostruct_html $html = null;
	public null|visuostruct_min  $min  = null;
	public null|visuostruct_ajax $ajax = null;

	# --- methods ---
	public function __construct ( null|\main $main = null ) {
		$this->main = $main;
	}

	public function html ( ) : visuostruct_html {
		if ( $this->html === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_html.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->html = new visuostruct_html( $this );
		}
		return $this->html;
	}

	public function min ( ) : visuostruct_min {
		if ( $this->min === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_min.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->min = new visuostruct_min( $this );
		}
		return $this->min;
	}

	public function ajax ( ) : visuostruct_ajax {
		if ( $this->ajax === null ) {
			$path = __DIR__ . DS . 'visuostruct' . DS . 'visuostruct_ajax.php';
			( include_once $path ) or error_file( $path , __FILE__ , __LINE__ );
			$this->ajax = new visuostruct_ajax( $this );
		}
		return $this->ajax;
	}

	# ---------------------------------------------------------------
	#   CURRENT — точка входа для visuorelease::final().
	#   1) если структура уже создана — вернуть её
	#   2) если нет — определить по контексту: min / ajax / html
	# ---------------------------------------------------------------
	public function current ( ) : null|object {
		if ( $this->min  !== null ) return $this->min;
		if ( $this->ajax !== null ) return $this->ajax;
		if ( $this->html !== null ) return $this->html;

		return match ( $this->detect_mode( ) ) {
			'min'  => $this->min( ) ,
			'ajax' => $this->ajax( ) ,
			default => $this->html( ) ,
		};
	}

	# ---------------------------------------------------------------
	#   DETECT MODE — какой формат ожидает клиент
	#   1) URL-параметр ?_mode=min|ajax|html
	#   2) заголовок X-Render-Mode
	#   3) по умолчанию — html
	# ---------------------------------------------------------------
	private function detect_mode ( ) : string {
		$get = $_GET[ '_mode' ] ?? null;
		if ( $get === 'min'  ) return 'min';
		if ( $get === 'ajax' ) return 'ajax';
		if ( $get === 'html' ) return 'html';

		$header = $_SERVER[ 'HTTP_X_RENDER_MODE' ] ?? null;
		if ( $header === 'min'  ) return 'min';
		if ( $header === 'ajax' ) return 'ajax';
		if ( $header === 'html' ) return 'html';

		return 'html';
	}
}

?>