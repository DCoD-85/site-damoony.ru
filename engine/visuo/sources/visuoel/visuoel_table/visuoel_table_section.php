<?PHP

# Зона таблицы. Дивный режим: <div class="ttype tbody">. HTML: <tbody>.

class visuoel_table_section extends visuoel_group_element {
	public string $zone = 'tbody';

	public function __construct ( null|visuoel $parent = null , null|visuoel__data $__data = null , string $zone = 'tbody' ) {
		parent::__construct( $parent , $__data );
		$this->zone = $zone;
	}

	protected function build ( ) : void {
		$html_mode = $this->detect_html_mode( );

		$vt = new visuotag( );
		if ( $html_mode ) {
			$vt = $vt->tag( $this->zone );
		} else {
			$vt = $vt->tag( 'div' , class : 'ttype ' . $this->zone );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function row ( ) : visuoel_table_row {
		$el = new visuoel_table_row( $this );
		$this->elements[ ] = $el;
		return $el;
	}

	private function detect_html_mode ( ) : bool {
		$p = $this->parent;
		while ( $p !== null ) {
			$d = $p->data( );
			if ( $d instanceof visuoel_table__data ) return $d->html_mode;
			$p = $p->parent;
		}
		return false;
	}
}