<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_news
	extends \algoritm_ {

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
		$user = $main->user;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$lang_code = (string) ( $url_base[ 0 ] ?? 'ru-ru' );

		$main->alliasglobal->head->title(
			$lang->get( 'PAGE_NEWS' , [ ] , 'News' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		$dp   = $main->dataprovider->current;
		$news = $dp->news->recent( 20 , $lang_code );

		$ve = new \visuoel( );

		$ve->block( )->html( )->data( )->set(
			'<h1 class="visuo-title">' . h( $lang->get( 'PAGE_NEWS' , [ ] , 'News' ) ) . '</h1>'
		);

		if ( empty( $news ) ) {
			$ve->message( )->info( )->data( )->set( $lang->get( 'NEWS_EMPTY' , [ ] , 'No news yet' ) );
			$main->alliasglobal->center->visuotag( )->el( $ve );
			return true;
		}

		$tz = $user->tz( );

		foreach ( $news as $n ) {
			$href = $link->href( false , [ $url_base[ 0 ] , 'news' , (string) $n[ 'slug' ] ] , use_all_adress_data : false );
			$date = ! empty( $n[ 'published_at' ] ) ? time_local( (string) $n[ 'published_at' ] , $tz ) : '';

			$item = '<article class="visuo-news-item">'
				. '<h2 class="visuo-news-title"><a href="' . h( $href ) . '">' . h( (string) $n[ 'title' ] ) . '</a></h2>'
				. ( $date !== '' ? '<div class="visuo-news-date">' . h( $date ) . '</div>' : '' )
				. '<div class="visuo-news-excerpt">' . h( (string) $n[ 'excerpt' ] ) . '</div>'
				. '<p class="visuo-news-more"><a href="' . h( $href ) . '">'
					. h( $lang->get( 'NEWS_READ_MORE' , [ ] , 'Read more' ) ) . '</a></p>'
				. '</article>';

			$ve->block( )->html( )->data( )->set( $item );
		}

		$main->alliasglobal->center->visuotag( )->el( $ve );
		
		return true;
	}
}