<?PHP

# visuostruct_html — контракт для HTML-страницы.
# Программист задаёт:
#   - visuotag — заменить содержимое блока
#   - false    — отключить блок
#   - null     — оставить дефолт из alliasglobal/local

class visuostruct_html {
	# --- refs ---
	public null|visuostruct $visuostruct = null;

	# --- blocks (переопределения) ---
	public null|visuotag|false $top    = null;
	public null|visuotag|false $left   = null;
	public null|visuotag|false $center = null;
	public null|visuotag|false $right  = null;
	public null|visuotag|false $bottom = null;

	# --- meta (переопределение) ---
	public null|visuometa $meta = null;

	# --- methods ---
	public function __construct ( null|visuostruct $visuostruct = null ) {
		$this->visuostruct = $visuostruct;
	}

	# Переопределить один или несколько блоков.
	# Не переданные — остаются null (дефолт).
	public function set (
		null|visuotag|false $top    = null ,
		null|visuotag|false $left   = null ,
		null|visuotag|false $center = null ,
		null|visuotag|false $right  = null ,
		null|visuotag|false $bottom = null
	) : static {
		if ( $top    !== null ) $this->top    = $top;
		if ( $left   !== null ) $this->left   = $left;
		if ( $center !== null ) $this->center = $center;
		if ( $right  !== null ) $this->right  = $right;
		if ( $bottom !== null ) $this->bottom = $bottom;
		return $this;
	}

	# Отключить блок (пусто вместо дефолта)
	public function off ( string $block ) : static {
		if ( property_exists( $this , $block ) ) {
			$this->{$block} = false;
		}
		return $this;
	}
}

?>