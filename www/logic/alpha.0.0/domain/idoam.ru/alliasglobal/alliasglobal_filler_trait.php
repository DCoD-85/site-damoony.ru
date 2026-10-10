<?PHP

namespace idoam_ru;

# =============================================================
#   alliasglobal_filler_trait — базовый API filler'а
#   Даёт методы show/hide/is_enabled для управления
#   без прямого доступа к свойству $enabled.
# =============================================================

trait alliasglobal_filler_trait {
	public bool $enabled = true;

	public function show ( ) : static {
		$this->enabled = true;
		return $this;
	}

	public function hide ( ) : static {
		$this->enabled = false;
		return $this;
	}

	public function is_enabled ( ) : bool {
		return $this->enabled;
	}
}