<?PHP

namespace damoony\visuo;

class algoritm_lang_downloads
	extends \algoritm_ {

	public array $available = [ '' ];
	public bool  $auth      = false;
	public bool  $search    = false;
	public bool  $default   = true;

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->render_list( ); }
	public function logged ( ) { return $this->render_list( ); }

		public function default ( ) {
		$url_base = $this->main->library->url->static_base;
		if ( ( $url_base[ 2 ] ?? '' ) !== '' ) {
			return false;   // мусор после /downloads/ → error_()
		}
		return $this->role( );
	}
	# ---------------------------------------------------------------
	private function render_list ( ) {
		$main = $this->main;
		$lang = $main->language;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$main->alliasglobal->head->title(
			$lang->get( 'DOWNLOADS_TITLE' , [ ] , 'Downloads' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		$dp    = $main->dataprovider->current;
		$files = $dp->files->all_active( );

		$ve = new \visuoel( );

		$ve->block( )->html( )->data( )->set(
			'<h1 class="visuo-title">' . h( $lang->get( 'DOWNLOADS_TITLE' , [ ] , 'Downloads' ) ) . '</h1>'
		);

		if ( empty( $files ) ) {
			$ve->message( )->info( )->data( )->set( $lang->get( 'DOWNLOADS_EMPTY' , [ ] , 'No files yet' ) );
			$main->alliasglobal->center->visuotag( )->el( $ve );
			return true;
		}

		foreach ( $files as $f ) {
			$href = $link->href( false , [ $url_base[ 0 ] , 'download' , (string) $f[ 'slug' ] ] , use_all_adress_data : false );
			$size = $this->human_size( (int) $f[ 'filesize' ] );

			$item  = '<article class="visuo-download-item">';
			$item .= '<h2 class="visuo-download-title">' . h( (string) $f[ 'title' ] ) . '</h2>';

			if ( ! empty( $f[ 'version' ] ) ) {
				$item .= '<div class="visuo-download-version">'
					. h( $lang->get( 'DOWNLOAD_VERSION' , [ ] , 'Version' ) ) . ': '
					. h( (string) $f[ 'version' ] ) . '</div>';
			}

			if ( ! empty( $f[ 'description' ] ) ) {
				$item .= '<div class="visuo-download-desc">' . h( (string) $f[ 'description' ] ) . '</div>';
			}

			$item .= '<div class="visuo-download-meta">'
				. h( $lang->get( 'DOWNLOAD_SIZE' , [ ] , 'Size' ) ) . ': ' . h( $size )
				. ' · ' . h( $lang->get( 'DOWNLOAD_COUNT' , [ ] , 'Downloads' ) ) . ': ' . (int) $f[ 'downloads' ]
				. '</div>';

			$item .= '<p class="visuo-download-btn"><a class="button button_download" href="' . h( $href ) . '">'
				. h( $lang->get( 'DOWNLOAD_BUTTON' , [ ] , 'Download' ) ) . '</a></p>';
			$item .= '</article>';

			$ve->block( )->html( )->data( )->set( $item );
		}

		$main->alliasglobal->center->visuotag( )->el( $ve );
		return true;
	}

	# ---------------------------------------------------------------
	private function human_size ( int $bytes ) : string {
		if ( $bytes < 1024 )                 return $bytes . ' B';
		if ( $bytes < 1024 * 1024 )          return round( $bytes / 1024 , 1 ) . ' KB';
		if ( $bytes < 1024 * 1024 * 1024 )   return round( $bytes / 1024 / 1024 , 2 ) . ' MB';
		return round( $bytes / 1024 / 1024 / 1024 , 2 ) . ' GB';
	}
}