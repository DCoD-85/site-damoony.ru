<?PHP

# visuoel_table — сетка/таблица.
#
# Два режима:
#   html_mode = false — дивная сетка (по умолчанию):
#     <div class="visuotable">
#       <div class="ttype tbody">
#         <div class="row">
#           <div class="col cols_30 colelsinline"><div class="col-inside">…</div></div>
#         </div>
#       </div>
#     </div>
#
#   html_mode = true — настоящая <table>:
#     <table class="visuotable_html" border="1" cellpadding="10">
#       <tbody><tr><td rowspan="2">…</td></tr></tbody>
#     </table>
#
# Зоны:
#   ->row()          — shortcut для ->body()->row()
#   ->head()->row()  — <div class="ttype thead"> / <thead>
#   ->body()->row()  — <div class="ttype tbody"> / <tbody>
#   ->foot()->row()  — <div class="ttype tfoot"> / <tfoot>

class visuoel_table extends visuoel_group {
	public bool $singleton = false;

	public null|visuoel_table_section $section_head = null;
	public null|visuoel_table_section $section_body = null;
	public null|visuoel_table_section $section_foot = null;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_table__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		if ( $d->html_mode ) {
			$vt = new visuotag( );
			$vt = $vt->tag( 'table' , class : 'visuotable_html' );
			foreach ( $d->html_attrs as $k => $v ) {
				$vt->attrs_add( $k , $v );
			}
		} else {
			$vt = new visuotag( );
			$vt = $vt->tag( 'div' , class : 'visuotable' );
		}

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}

	public function html_mode ( bool $mode = true ) : static {
		$this->__data->html_mode = $mode;
		return $this;
	}

	public function attr ( string $name , string $value ) : static {
		$this->__data->html_attrs[ $name ] = $value;
		return $this;
	}

	# --- factories ---

	public function row ( ) : visuoel_table_row {
		return $this->body( )->row( );
	}

	public function head ( ) : visuoel_table_section {
		if ( $this->section_head === null ) {
			$this->section_head = new visuoel_table_section( $this , zone : 'thead' );
			$this->elements[ ] = $this->section_head;
		}
		return $this->section_head;
	}

	public function body ( ) : visuoel_table_section {
		if ( $this->section_body === null ) {
			$this->section_body = new visuoel_table_section( $this , zone : 'tbody' );
			$this->elements[ ] = $this->section_body;
		}
		return $this->section_body;
	}

	public function foot ( ) : visuoel_table_section {
		if ( $this->section_foot === null ) {
			$this->section_foot = new visuoel_table_section( $this , zone : 'tfoot' );
			$this->elements[ ] = $this->section_foot;
		}
		return $this->section_foot;
	}
}

class visuoel_table__data extends visuoel_group__data {
	public bool $html_mode = false;
	public array $html_attrs = [ ];
}