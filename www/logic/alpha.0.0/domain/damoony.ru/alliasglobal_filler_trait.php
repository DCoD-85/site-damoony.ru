<?PHP

namespace damoony;

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