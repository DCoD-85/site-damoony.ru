<?PHP

# visuoel_group — база для групп (form, primer, input, table, block, link, user).
# $singleton наследуется от visuoel: по умолчанию true.
# Группа-наследник может переопределить: protected $singleton = false.

class visuoel_group extends visuoel {
	protected string $css_class = 'group';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_group__data( $this );
	}
}

class visuoel_group__data extends visuoel__data {
}