<?PHP

namespace damoony\visuo;

class algoritm_lang_news_page
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = true;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render_page( ); }
	public function logged ( ) { return $this->render_page( ); }

	# ---------------------------------------------------------------
	private function render_page ( ) {
		$main = $this->main;
		$lang = $main->language;
		$user = $main->user;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$lang_code = (string) ( $url_base[ 0 ] ?? 'ru-ru' );
		$slug      = (string) ( $url_base[ 2 ] ?? '' );

		$dp = $main->dataprovider->current;
		$n  = $dp->news->by_slug( $slug , $lang_code );

		# fallback на ru-ru
		$fallback = false;
		if ( empty( $n ) && $lang_code !== 'ru-ru' ) {
			$n = $dp->news->by_slug( $slug , 'ru-ru' );
			if ( ! empty( $n ) ) $fallback = true;
		}

		$back_href = $link->href( false , [ $url_base[ 0 ] , 'news' ] , use_all_adress_data : false );

		$ve = new \visuoel( );

		# --- не найдено ---
		if ( empty( $n ) ) {
			$main->alliasglobal->head->title(
				$lang->get( 'NEWS_NOT_FOUND' , [ ] , 'News not found' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
			);

			$ve->message( )->error( )->data( )->set( $lang->get( 'NEWS_NOT_FOUND' , [ ] , 'News not found' ) );

			$ve->block( )->html( )->data( )->set(
				'<p class="visuo-back-link"><a href="' . h( $back_href ) . '">← '
				. h( $lang->get( 'NEWS_TITLE' , [ ] , 'News' ) ) . '</a></p>'
			);

			$main->alliasglobal->center->visuotag( )->el( $ve );
			return true;
		}

		# --- найдено ---
		$main->alliasglobal->head->title(
			(string) $n[ 'title' ] . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		$tz   = $user->tz( );
		$date = ! empty( $n[ 'published_at' ] ) ? time_local( (string) $n[ 'published_at' ] , $tz ) : '';

		if ( $fallback ) {
			$ve->message( )->warning( )->data( )->set(
				$lang->get( 'DOCS_TRANSLATION_PENDING' , [ ] , 'Translation pending' )
			);
		}

		$article = '<article class="visuo-news-item visuo-news-full">'
			. '<h1 class="visuo-news-title">' . h( (string) $n[ 'title' ] ) . '</h1>'
			. ( $date !== '' ? '<div class="visuo-news-date">' . h( $date ) . '</div>' : '' )
			. '<div class="visuo-news-content">' . (string) $n[ 'content_html' ] . '</div>'
			. '</article>'
			. '<p class="visuo-back-link"><a href="' . h( $back_href ) . '">← '
			. h( $lang->get( 'NEWS_TITLE' , [ ] , 'News' ) ) . '</a></p>';

		$ve->block( )->html( )->data( )->set( $article );

		$main->alliasglobal->center->visuotag( )->el( $ve );
		return true;
	}
}