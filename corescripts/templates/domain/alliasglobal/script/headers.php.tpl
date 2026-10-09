<?PHP

namespace <domain_ns>;

class alliasglobal_script_headers extends alliasglobalscript {
	# --- state ---
	public string $script_name = 'headers';

	# --- custom ---
	public array  $langs_available = [ 'ru-ru' , 'en-en' , 'ch-ch' ];
	public string $lang_default    = 'ru-ru';

	# --- methods ---
	# Скрипт создаётся в alliasglobal::init() — до запуска алгоритма.
	# Редирект в init() безопасен (заголовки ещё не отправлены).
	protected function init ( ) : void {
		$this->handle_language( );
		$this->handle_skin( );
	}

	# ---------------------------------------------------------------
	#   LANGUAGE
	#   Правила:
	#     1. Язык всегда первый сегмент URL.
	#     2. Приоритет языка: URL → куки → default.
	#     3. Куки ставятся, если отсутствуют или не совпадают с целевым.
	#     4. Если URL без языка — 307 на URL с языком.
	# ---------------------------------------------------------------
	protected function handle_language ( ) : void {
		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;
		$url_vars   = $url->static_vars;

		$base0       = $url_base[ 0 ] ?? '';
		$cookie_lang = $_COOKIE[ 'lang' ] ?? '';

		$is_lang_url  = in_array( $base0       , $this->langs_available , true );
		$cookie_valid = in_array( $cookie_lang , $this->langs_available , true );

		# --- 1. целевой язык ---
		if ( $is_lang_url ) {
			$lang = $base0;                          # URL задаёт язык
		} elseif ( $cookie_valid ) {
			$lang = $cookie_lang;                    # иначе — из куки
		} else {
			$lang = $this->lang_default;             # иначе — дефолт
		}

		# --- 2. куки ---
		if ( ! $cookie_valid || $cookie_lang !== $lang ) {
			$exp    = time( ) + 1000000;
			$path   = '/';
			$domain = $url_allias[ 0 ] ?? '';

			setcookie( 'language' , $lang , $exp , $path , $domain );
			setcookie( 'lang'     , $lang , $exp , $path , $domain );
		}

		# --- 3. URL всегда должен начинаться с языка ---
		if ( ! $is_lang_url ) {
			# если [0] похож на код языка (ru, ru-ru, en, en-en) — заменяем,
			# иначе — добавляем язык в начало
			if ( mb_ereg( '^[a-z]{2}(-[a-z]{2})?$' , $base0 ) ) {
				$url_base[ 0 ] = $lang;
				$new_url_base  = $url_base;
			} else {
				$new_url_base = array_merge( [ $lang ] , $url_base );
			}

			$href = $link->href( $url_domain , $new_url_base , $url_vars );

			http_response_code( 307 );
			header( 'Location: ' . $href );
			exit;
		}
	}

	# ---------------------------------------------------------------
	#   SKIN
	#   ?skin=... → кука + редирект на URL без ?skin.
	# ---------------------------------------------------------------
	protected function handle_skin ( ) : void {
		$skin = $_GET[ 'skin' ] ?? '';
		if ( $skin === '' ) return;

		$main = $this->main;
		$lib  = $main->library;
		$url  = $lib->url( );
		$link = $lib->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base   = $url->static_base;

		setcookie( 'skin' , $skin , 0 , '/' , $url_allias[ 0 ] ?? '' );

		$href = $link->href( $url_domain , $url_base , use_all_adress_data : false );

		header( 'Location: ' . $href , true );
		exit;
	}
}