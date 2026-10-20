<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_docs_page
	extends \algoritm_ {

	# ---------------------------------------------------------------
	

	public function guest  ( ) { return $this->render_page( ); }
	public function logged ( ) { return $this->render_page( ); }

	# ---------------------------------------------------------------
	private function render_page ( ) {
		$main = $this->main;
		$lang = $main->language;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$slug = (string) ( $url_base[ 2 ] ?? '' );

		$dp = $main->dataprovider->current;
		$a  = $dp->doc->article_by_slug( $slug );

		$back_href = $link->href( false , [ $url_base[ 0 ] , 'docs' ] , use_all_adress_data : false );

		$ve = new \visuoel( );

		# --- не найдено ---
		if ( empty( $a ) ) {
			http_response_code( 404 );

			$main->alliasglobal->head->title(
				$lang->get( 'DOCS_ARTICLE_NOT_FOUND' , [ ] , 'Article not found' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
			);

			$ve->message( )->error( )->data( )->set(
				$lang->get( 'DOCS_ARTICLE_NOT_FOUND' , [ ] , 'Article not found' )
			);

			$ve->block( )->html( )->data( )->set(
				'<p class="visuo-back-link"><a href="' . h( $back_href ) . '">← '
				. h( $lang->get( 'DOCS_TITLE' , [ ] , 'Documentation' ) ) . '</a></p>'
			);

			$main->alliasglobal->center->visuotag( )->el( $ve );
			return true;
		}

		# --- найдено ---
		$main->alliasglobal->head->title(
			(string) $a[ 'title' ] . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		# --- хлебные крошки ---
		$crumbs = '<nav class="visuo-breadcrumbs">'
			. '<a href="' . h( $link->href( false , [ $url_base[ 0 ] , 'index' ] , use_all_adress_data : false ) ) . '">'
				. h( $lang->get( 'MENU_HOME' , [ ] , 'Home' ) ) . '</a> / '
			. '<a href="' . h( $back_href ) . '">' . h( $lang->get( 'DOCS_TITLE' , [ ] , 'Documentation' ) ) . '</a> / '
			. ( ! empty( $a[ 'section_title' ] ) ? h( (string) $a[ 'section_title' ] ) . ' / ' : '' )
			. '<span>' . h( (string) $a[ 'title' ] ) . '</span>'
			. '</nav>';

		$ve->block( )->html( )->data( )->set( $crumbs );

		# --- контент ---
		$content = '<article class="visuo-doc-article">'
			. '<h1 class="visuo-doc-title">' . h( (string) $a[ 'title' ] ) . '</h1>'
			. ( ! empty( $a[ 'excerpt' ] ) ? '<p class="visuo-doc-excerpt">' . h( (string) $a[ 'excerpt' ] ) . '</p>' : '' )
			. '<div class="visuo-doc-content">' . (string) $a[ 'content_html' ] . '</div>'
			. '</article>'
			. '<p class="visuo-back-link"><a href="' . h( $back_href ) . '">← '
			. h( $lang->get( 'DOCS_TITLE' , [ ] , 'Documentation' ) ) . '</a></p>';

		$ve->block( )->html( )->data( )->set( $content );

		$main->alliasglobal->center->visuotag( )->el( $ve );
		return true;
	}
}