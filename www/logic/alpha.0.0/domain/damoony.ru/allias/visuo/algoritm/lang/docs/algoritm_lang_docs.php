<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_docs
	extends \algoritm_ {

	public array $available = [ '' , '([a-z0-9-]+)|page' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render_list( ); }
	public function logged ( ) { return $this->render_list( ); }

	# ---------------------------------------------------------------
	private function render_list ( ) {
		$main = $this->main;
		$lang = $main->language;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$main->alliasglobal->head->title(
			$lang->get( 'DOCS_TITLE' , [ ] , 'Documentation' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		$dp       = $main->dataprovider->current;
		$sections = $dp->doc->sections_tree( true );

		$ve = new \visuoel( );

		$ve->block( )->html( )->data( )->set(
			'<h1 class="visuo-title">' . h( $lang->get( 'DOCS_TITLE' , [ ] , 'Documentation' ) ) . '</h1>'
		);

		if ( empty( $sections ) ) {
			$ve->message( )->info( )->data( )->set( $lang->get( 'DOCS_EMPTY' , [ ] , 'No articles yet' ) );
			$main->alliasglobal->center->visuotag( )->el( $ve );
			return true;
		}

		# --- дерево разделов с их статьями ---
		foreach ( $sections as $s ) {
			$articles = $dp->doc->articles_by_section( (int) $s[ 'id' ] , true );

			$html  = '<section class="visuo-doc-section">';
			$html .= '<h2 class="visuo-doc-section-title">' . h( (string) $s[ 'title' ] ) . '</h2>';

			if ( ! empty( $s[ 'description' ] ) ) {
				$html .= '<p class="visuo-doc-section-desc">' . h( (string) $s[ 'description' ] ) . '</p>';
			}

			if ( empty( $articles ) ) {
				$html .= '<p class="visuo-doc-empty">' . h( $lang->get( 'DOCS_EMPTY' , [ ] , 'No articles yet' ) ) . '</p>';
			} else {
				$html .= '<ul class="visuo-doc-articles">';
				foreach ( $articles as $a ) {
					$href  = $link->href( false , [ $url_base[ 0 ] , 'docs' , (string) $a[ 'slug' ] ] , use_all_adress_data : false );
					$html .= '<li><a href="' . h( $href ) . '">' . h( (string) $a[ 'title' ] ) . '</a></li>';
				}
				$html .= '</ul>';
			}

			$html .= '</section>';

			$ve->block( )->html( )->data( )->set( $html );
		}

		$main->alliasglobal->center->visuotag( )->el( $ve );
		return true;
	}
}