<?PHP

# visuoel_group — база для всех групп (input, form, table, block, link, user).
# Обёртка: <div class="group"> ... </div>.

class visuoel_group extends visuoel {
	protected string $css_class = 'group';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group__data( $this );
	}
}

class visuoel_group__data extends visuoel__data {
}