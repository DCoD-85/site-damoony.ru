<?PHP

namespace damoony_ru\visuo;

class algoritm_lang_download_page
	extends \algoritm_ {

	# ---------------------------------------------------------------
	public function release ( ) {
		$this->algoritmdata->set( $this->available , $this->auth , $this->search , $this->default );
		$this->release_( $this );
	}

	public function guest  ( ) { return $this->serve( ); }
	public function logged ( ) { return $this->serve( ); }

	# ---------------------------------------------------------------
	private function serve ( ) {
		$main = $this->main;
		$lang = $main->language;

		$lib      = $main->library;
		$link     = $lib->link( );
		$url      = $lib->url( );
		$url_base = $url->static_base;

		$slug      = (string) ( $url_base[ 2 ] ?? '' );
		$back_href = $link->href( false , [ $url_base[ 0 ] , 'downloads' ] , use_all_adress_data : false );

		$dp = $main->dataprovider->current;
		$f  = $dp->files->by_slug( $slug );

		# --- файл не найден / скрыт ---
		if ( empty( $f ) || ( $f[ 'status' ] ?? '' ) !== 'active' ) {
			return $this->not_found( $back_href );
		}

		# --- защита от path traversal ---
		$filename = basename( (string) $f[ 'filename' ] );
		$base_dir = realpath( VISUO_MEDIA_DOWNLOADS );
		$path     = realpath( VISUO_MEDIA_DOWNLOADS . $filename );

		if ( $base_dir === false || $path === false || ! str_starts_with( $path , $base_dir ) ) {
			return $this->not_found( $back_href );
		}

		# --- лог скачивания (до отправки) ---
		$user_id = $main->user->is_guest( ) ? null : $main->user->id( );
		$ip      = (string) ( $_SERVER[ 'REMOTE_ADDR' ] ?? '' );
		$dp->files->increment_downloads( (int) $f[ 'id' ] , $user_id , $ip );

		# --- заголовки и отдача ---
		$mime = (string) ( $f[ 'mime_type' ] ?? 'application/octet-stream' );
		$size = (int) filesize( $path );

		header( 'Content-Type: ' . $mime );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Length: ' . $size );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Cache-Control: private, no-store' );

		readfile( $path );
		exit;
	}

	# ---------------------------------------------------------------
	private function not_found ( string $back_href ) {
		$main = $this->main;
		$lang = $main->language;

		http_response_code( 404 );

		$main->alliasglobal->head->title(
			$lang->get( 'DOWNLOAD_FILE_NOT_FOUND' , [ ] , 'File not found' ) . ' — ' . $lang->get( 'VISUO_TITLE' , [ ] , 'visuo' )
		);

		$ve = new \visuoel( );
		$ve->message( )->error( )->data( )->set(
			$lang->get( 'DOWNLOAD_FILE_NOT_FOUND' , [ ] , 'File not found' )
		);
		$ve->block( )->html( )->data( )->set(
			'<p class="visuo-back-link"><a href="' . h( $back_href ) . '">← '
			. h( $lang->get( 'DOWNLOADS_TITLE' , [ ] , 'Downloads' ) ) . '</a></p>'
		);

		$main->alliasglobal->center->visuotag( )->el( $ve );
		return true;
	}
}