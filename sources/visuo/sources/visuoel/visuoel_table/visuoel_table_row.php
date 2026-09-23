<?PHP

# Строка. Дивный режим: <div class="row">. HTML: <tr>.

class visuoel_table_row extends visuoel_group_element {
	protected function build ( ) : void {
		$html_mode = $this->detect_html_mode( );

		$vt = new visuotag( );
		if ( $html_mode ) {
			$vt = $vt->tag( 'tr' );
		} else {
			$vt = $vt->tag( 'div' , class : 'row' );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function cell ( int $width = 0 , int $colspan = 1 , int $rowspan = 1 , bool $inline = false ) : visuoel_table_cell {
		$el = new visuoel_table_cell( $this );
		$el->data( )->width( $width )->colspan( $colspan )->rowspan( $rowspan )->inline( $inline );
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