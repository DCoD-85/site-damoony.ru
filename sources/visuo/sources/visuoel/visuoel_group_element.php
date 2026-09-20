<?PHP

# visuoel_group_element — база для элементов групп.
# Обёртка по умолчанию: <div class="element">[children]</div>.

class visuoel_group_element extends visuoel {
	protected string $css_class = 'element';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group_element__data( $this );
	}
}

class visuoel_group_element__data extends visuoel__data {
	# --- state ---
	public bool $readonly = false;
	public bool $disabled = false;
}