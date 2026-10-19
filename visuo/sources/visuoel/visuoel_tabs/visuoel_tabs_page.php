<?PHP

class visuoel_tabs_page extends visuoel_group_element {
	protected string $css_class = 'tab_page';

	public null|visuotag $vt_title = null;

	public function data ( ) : visuoel_tabs_page__data {
		/** @var visuoel_tabs_page__data $data */
		$data = $this->__data;
		return $data;
	}

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_tabs_page__data( $this );
	}

	protected function build ( ) : void {
		$id = 'tab_' . spl_object_id( $this );
		$d  = $this->data();

		$title = new visuotag( );
		$title = $title->tag( 'span' , class : 'tab_title' );
		$title->data_add( 'tab-target' , $id , false );
		$title->data_add( 'action' , 'tabs-show' , false );
		if ( $d->title !== '' ) $title->content( $d->esc( $d->title ) );
		$this->vt_title = $title;

		$page = new visuotag( );
		$page = $page->tag( 'div' , class : 'tab_page' , id : $id );

		$this->vt_start = $page;
		$this->vt_end   = $page;
	}
}

class visuoel_tabs_page__data extends visuoel_group_element__data {
	public string $title = '';

	public function set ( string $title ) : static {
		$this->title = $title;
		return $this;
	}
}