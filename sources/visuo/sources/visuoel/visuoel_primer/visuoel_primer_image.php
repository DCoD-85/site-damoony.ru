<?PHP

# visuoel_primer_image — элемент primer\image.
# Обёртка: <div class="image"><img src="…" alt="…"></div>.
# Лист: vt_start === vt_end.

class visuoel_primer_image extends visuoel_group_element {
	protected string $tag       = 'div';
	protected string $css_class = 'image';

	protected function make_default_data ( ) : visuoel__data {
		return new visuoel_primer_image__data( $this );
	}

	protected function build ( ) : void {
		$d = $this->__data;

		$vt = new visuotag( );
		$vt = $vt->tag( 'div' , class : $this->css_class );

		$img = $vt->tag( 'img' , tag_closed : false );
		if ( $d->src ) $img->attrs_add( 'src' , $d->src );
		if ( $d->alt !== '' ) $img->attrs_add( 'alt' , $d->alt );
		if ( $d->title ) $img->attrs_add( 'title' , $d->title );
		if ( $d->width ) $img->attrs_add( 'width' , (string) $d->width );
		if ( $d->height ) $img->attrs_add( 'height' , (string) $d->height );
		$img->parent( );

		$this->vt_start = $vt;
		$this->vt_end   = $vt;
	}
}

class visuoel_primer_image__data extends visuoel_group_element__data {
	# --- state ---
	public string $src    = '';
	public string $alt    = '';
	public string $title  = '';
	public int    $width  = 0;
	public int    $height = 0;

	# --- methods ---
	public function set ( string $src , string $alt = '' , string $title = '' , int $width = 0 , int $height = 0 ) : static {
		$this->src    = $src;
		$this->alt    = $alt;
		$this->title  = $title;
		$this->width  = $width;
		$this->height = $height;
		return $this;
	}
}