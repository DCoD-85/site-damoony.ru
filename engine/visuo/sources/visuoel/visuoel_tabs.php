<?PHP

class visuoel_tabs extends visuoel_group {
	protected string $css_class = 'tabs';
	public bool $singleton = false;

	public null|visuotag $vt_head = null;
	public null|visuotag $vt_body = null;

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_tabs__data( $this );
	}

	protected function build ( ) : void {
		$tabs = new visuotag( );
		$tabs = $tabs->tag( 'div' , class : 'tabs' );

		$head = $tabs->tag( 'div' , class : 'hhead' );
		$body = $tabs->tag( 'div' , class : 'bbody' );

		$this->vt_start = $tabs;
		$this->vt_head  = $head;
		$this->vt_body  = $body;
		$this->vt_end   = $body;
	}

	public function init ( ) : void {
		if ( $this->init ) return;
		$this->init = true;

		$this->build( );

		$first = true;
		foreach ( $this->elements as $page ) {
			if ( ! $page instanceof visuoel_tabs_page ) continue;
			$page->init( );

			if ( $first ) {
				if ( $page->vt_title !== null ) $page->vt_title->attrs_class_add( ' selected' );
				if ( $page->vt_start !== null ) $page->vt_start->attrs_class_add( ' selected' );
				$first = false;
			}

			if ( $page->vt_title !== null ) $this->vt_head->append( $page->vt_title );
			if ( $page->vt_start !== null ) $this->vt_body->append( $page->vt_start );
		}
	}

	public function page ( null|visuoel__data $__data = null ) : visuoel_tabs_page {
		$data = $__data instanceof visuoel_tabs_page__data ? $__data : null;
		$el = new visuoel_tabs_page( $this , $data );
		$this->elements[ ] = $el;
		return $el;
	}
}

class visuoel_tabs__data extends visuoel_group__data {
}