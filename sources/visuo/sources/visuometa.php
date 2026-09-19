<?PHP

class visuometa {
	# --- refs ---
	public null|visuorelease $visuorelease = null;

	# --- state ---
	public array $elements = [ ];

	# --- legacy ---
	public null|visuotag $visuotag = null;   # deprecated, оставлено для совместимости

	# --- methods ---
	public function __construct ( null|visuorelease $visuorelease = null ) {
		$this->visuorelease = $visuorelease;
		$this->visuotag     = new visuotag( );

		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Создаем объект visuometa.' , __METHOD__ , __FILE__ , __LINE__ );
		}
	}

	public function visuorelease ( ) : null|visuorelease {
		return $this->visuorelease;
	}

	# --- factories ---

	public function title ( ) : visuometa_title {
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Создаем тег TITLE.' , __METHOD__ , __FILE__ , __LINE__ );
		}
		$title = new visuometa_title( $this );
		$this->elements[ ] = $title;
		return $title;
	}

	public function link ( ) : visuolink {
		if ( $this->visuorelease ) {
			$this->visuorelease->visuolog->add( 'Создаем тег LINK.' , __METHOD__ , __FILE__ , __LINE__ );
		}
		$link = new visuolink( $this );
		$this->elements[ ] = $link;
		return $link;
	}

	# --- state ---

	public function clear ( ) : visuometa {
		$this->elements = [ ];
		return $this;
	}

	# --- deprecated ---

	public function visuotag ( ) : null|visuotag {
		return $this->visuotag;
	}

	public function visuotag_set ( visuotag $visuotag ) : visuometa {
		$this->visuotag = $visuotag;
		return $this;
	}

	# --- release ---

	# Собрать все элементы в одно дерево visuotag.
	# Каждый вызов создаёт новый visuotag — безопасно вызывать повторно.
	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		foreach ( $this->elements as $el ) {
			$vt->append( $el->as_tag( ) );
		}
		return $vt;
	}
}

class visuometa_title {
	# --- refs ---
	public null|visuometa $visuometa = null;

	# --- state ---
	public string $content = '';

	# --- methods ---
	public function __construct ( visuometa $visuometa ) {
		$this->visuometa = $visuometa;
	}

	public function back ( ) : visuometa {
		return $this->visuometa;
	}

	public function data ( string $content = '' ) : visuometa_title {
		$this->content = $content;
		return $this;
	}

	public function as_tag ( ) : visuotag {
		$vt = new visuotag( );
		$vt->tag( 'title' )->content( $this->content );
		return $vt;
	}
}

?>