<?PHP

# visuoel_group — база для групп (form, input, table, block, link, user).
# Обёртка по умолчанию: <div class="group">[vt_end === vt_start]</div>.
# Конкретные группы переопределяют $css_class, а при необходимости — build().

class visuoel_group extends visuoel {
	protected string $css_class = 'group';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group__data( $this );
	}
}

class visuoel_group__data extends visuoel__data {
}