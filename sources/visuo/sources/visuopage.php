<?PHP

# visuopage — применяет переопределения из visuostruct_html
# к дереву visuorelease->visuotag (то, что будет отрендерено).
#
# Работает по классу слота: dt_top, dt_left, dt_center, dt_right, dt_bottom.
# Схема уже вставила туда дефолт из alliasglobal — apply() его заменяет,
# если программист задал override в структуре.

class visuopage {
	# --- refs ---
	public null|\main $main = null;

	# --- methods ---
	public function __construct ( null|\main $main = null ) {
		$this->main = $main;
	}

	public function apply ( visuostruct_html $struct ) : void {
		$main = $this->main;
		if ( $main === null ) return;
		if ( $main->visuorelease === null ) return;
		if ( $main->visuorelease->visuotag === null ) return;

		$vt_root = $main->visuorelease->visuotag->root( );
		if ( $vt_root === null ) return;

		$blocks = [ 'top' , 'left' , 'center' , 'right' , 'bottom' ];

		foreach ( $blocks as $name ) {
			$override = $struct->{$name};
			if ( $override === null ) continue;   # дефолт — не трогаем

			$slot = $vt_root->find( class : 'dt_' . $name );
			if ( $slot === null ) continue;

			if ( $override === false ) {
				$slot->clear( );
				continue;
			}

			if ( $override instanceof visuotag ) {
				$slot->clear( )->append( $override );
			}
		}
	}
}

?>