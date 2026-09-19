<?PHP

# ------------------------------------------------------------
#  visuostruct_ajax — структура AJAX-ответа
#  Собирает JSON: { error, address, layers: { header, top, left, center, right, bottom } }
# ------------------------------------------------------------

class visuostruct_ajax {
	# --- refs ---
	public null|visuostruct $visuostruct = null;

	# --- state ---
	public bool $error = false;

	# --- layers ---
	public null|visuostruct_ajax_address $address = null;
	public null|visuostruct_ajax_header  $header  = null;
	public null|visuostruct_ajax_top     $top     = null;
	public null|visuostruct_ajax_left    $left    = null;
	public null|visuostruct_ajax_center  $center  = null;
	public null|visuostruct_ajax_right   $right   = null;
	public null|visuostruct_ajax_bottom  $bottom  = null;

	# --- methods ---
	public function __construct ( null|visuostruct $visuostruct = null ) {
		$this->visuostruct = $visuostruct;
	}

	public function address ( ) : visuostruct_ajax_address { return $this->address ??= new visuostruct_ajax_address( $this ); }
	public function header  ( ) : visuostruct_ajax_header  { return $this->header  ??= new visuostruct_ajax_header ( $this ); }
	public function top     ( ) : visuostruct_ajax_top     { return $this->top     ??= new visuostruct_ajax_top    ( $this ); }
	public function left    ( ) : visuostruct_ajax_left    { return $this->left    ??= new visuostruct_ajax_left   ( $this ); }
	public function center  ( ) : visuostruct_ajax_center  { return $this->center  ??= new visuostruct_ajax_center ( $this ); }
	public function right   ( ) : visuostruct_ajax_right   { return $this->right   ??= new visuostruct_ajax_right  ( $this ); }
	public function bottom  ( ) : visuostruct_ajax_bottom  { return $this->bottom  ??= new visuostruct_ajax_bottom ( $this ); }

	public function as_json ( ) : string {
		$structs = [
			'error'   => $this->error ,
			'address' => $this->address?->release( ) ,
			'layers'  => [
				'header' => $this->header?->release( ) ,
				'top'    => $this->top?->release( ) ,
				'left'   => $this->left?->release( ) ,
				'center' => $this->center?->release( ) ,
				'right'  => $this->right?->release( ) ,
				'bottom' => $this->bottom?->release( ) ,
			] ,
		];

		$json = json_encode( $structs , JSON_UNESCAPED_UNICODE );
		return $json === false ? '{}' : $json;
	}
}

# ------------------------------------------------------------
#  Base для всех ajax-элементов
# ------------------------------------------------------------

abstract class visuostruct_ajax_element {
	# --- refs ---
	public null|visuostruct      $visuostruct      = null;
	public null|visuostruct_ajax $visuostruct_ajax = null;

	# --- methods ---
	public function __construct ( visuostruct_ajax $visuostruct_ajax ) {
		$this->visuostruct_ajax = $visuostruct_ajax;
		$this->visuostruct      = $visuostruct_ajax->visuostruct;
	}
}

# ------------------------------------------------------------
#  address — URL текущей страницы
# ------------------------------------------------------------

class visuostruct_ajax_address extends visuostruct_ajax_element {
	# --- state ---
	public null|string $address = null;

	public function set ( null|string $address ) : void {
		$this->address = $address;
	}

	public function astag ( ) : null|string {
		return $this->address;
	}

	public function release ( ) : null|string {
		return $this->address;
	}
}

# ------------------------------------------------------------
#  header — html без layer-обёртки
# ------------------------------------------------------------

class visuostruct_ajax_header extends visuostruct_ajax_element {
	# --- state ---
	public null|visuotag $visuotag = null;

	public function set ( null|visuotag $visuotag ) : void {
		$this->visuotag = $visuotag;
	}

	public function astag ( ) : null|visuotag {
		return $this->visuotag;
	}

	public function release ( ) : null|string {
		return $this->visuotag?->root( )->release( );
	}
}

# ------------------------------------------------------------
#  Базовый слой: visuotag + layer-метка
# ------------------------------------------------------------

abstract class visuostruct_ajax_layer extends visuostruct_ajax_element {
	# --- state ---
	public null|visuotag $visuotag = null;

	# имя слоя в DOM (dt_top, dt_left, ...)
	abstract public function layer ( ) : string;

	public function set ( null|visuotag $visuotag ) : void {
		$this->visuotag = $visuotag;
	}

	public function astag ( ) : null|visuotag {
		return $this->visuotag;
	}

	public function release ( ) : null|array {
		if ( $this->visuotag === null ) return null;
		return [
			'layer' => $this->layer( ) ,
			'html'  => $this->visuotag->root( )->release( ) ,
		];
	}
}

# ------------------------------------------------------------
#  Слои (top, left, center, right, bottom)
# ------------------------------------------------------------

class visuostruct_ajax_top    extends visuostruct_ajax_layer { public function layer ( ) : string { return 'dt_top';    } }
class visuostruct_ajax_left   extends visuostruct_ajax_layer { public function layer ( ) : string { return 'dt_left';   } }
class visuostruct_ajax_center extends visuostruct_ajax_layer { public function layer ( ) : string { return 'dt_center'; } }
class visuostruct_ajax_right  extends visuostruct_ajax_layer { public function layer ( ) : string { return 'dt_right';  } }
class visuostruct_ajax_bottom extends visuostruct_ajax_layer { public function layer ( ) : string { return 'dt_bottom'; } }

?>