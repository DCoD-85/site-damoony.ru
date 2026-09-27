<?PHP

# visuostruct_html — контракт для HTML-страницы.
# Позволяет программисту переопределить блоки из alliasglobal.
#
# Если программист не создаёт эту структуру — блоки берутся из alliasglobal автоматически.

class visuostruct_html {
	# --- refs ---
	public null|visuostruct $visuostruct = null;

	# --- blocks (переопределения) ---
	public null|visuotag $top    = null;
	public null|visuotag $left   = null;
	public null|visuotag $center = null;
	public null|visuotag $right  = null;
	public null|visuotag $bottom = null;

	# --- meta (переопределение) ---
	public null|visuometa $meta = null;

	# --- methods ---
	public function __construct ( null|visuostruct $visuostruct = null ) {
		$this->visuostruct = $visuostruct;
	}

	public function set (
		null|visuotag $top    = null ,
		null|visuotag $left   = null ,
		null|visuotag $center = null ,
		null|visuotag $right  = null ,
		null|visuotag $bottom = null
	) : static {
		$this->top    = $top;
		$this->left   = $left;
		$this->center = $center;
		$this->right  = $right;
		$this->bottom = $bottom;
		return $this;
	}
}

?>