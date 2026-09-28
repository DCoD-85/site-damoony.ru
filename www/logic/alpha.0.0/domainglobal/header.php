<?PHP
class domainglobal_header {
	# vars
	public main $main;
	public visuorelease $visuorelease;
	# methods
	public function __construct ( main $main ) {
		$this->main = $main;

		$new_url_base = null;

		$library = $main->library;
		$link = $library->link( );
		$url = $library->url( );
		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		
		$lang = in_array( $url_base[ 0 ] , [ 'ru-ru' , 'en-en' , 'tests' ] );
		$lang_cookie = in_array( $_COOKIE[ 'lang' ] ?? '' , [ 'ru-ru' , 'en-en' , 'tests' ] );
		
		if( $lang ){
			$lang = $url_base[ 0 ];
		} else if( ! $lang ){
			# $lang = in_array( $_COOKIE[ 'language' ] , [ 'ru-ru' , 'en-en' ] );
			# print 'lang 2: ' . $lang . '<br />';
			# $lang = ( $lang ? $_COOKIE[ 'language' ] : false );
			# print 'lang 3: ' . $lang . '<br />';
			$lang = $lang_cookie ? $_COOKIE[ 'lang' ] : 'ru-ru';

			$new_lang = $lang ?: 'ru-ru';
			# print 'lang 3: ' . $lang . '<br />';
			if( mb_ereg( '^[a-z]{2}-[a-z]{2}$' , $url_base[ 0 ] ) ){
				$url_base[ 0 ] = $new_lang;
				$new_url_base = $url_base;
			} else {
				$new_url_base = array_merge( [ $new_lang ] , $url_base );
			}
		}

		if( $lang_cookie ){
			$lang_cookie = $_COOKIE[ 'lang' ];
			
			# print $lang . '<br />' . $lang_cookie . '<br />';
			if( $lang !== $lang_cookie ){
				$lang_cookie = false;
			}
		}
		if( ! $lang_cookie ){
			$value = $lang;
			$exp = time( ) + 1000000;
			$path = '/';
			$domain = $url_allias[ 0 ];
			setcookie( 'language' , $value , $exp , $path , $domain );
			$value = $lang;
			setcookie( 'lang' , $value , $exp , $path , $domain );
			
			if( $new_url_base ){
				$ahref = $link->href( $url_domain , $new_url_base , $url_vars );
				# die( 'ahref: ' . $ahref );
				http_response_code( 307 );
				header( 'Location: ' . $ahref );
			} else {
				$ahref = $link->href( $url_domain , $url_base , $url_vars );
				http_response_code( 307 );
				header( 'Location: ' . $ahref );
			}
		}
		
		
		if( $new_url_base ){
			$ahref = $link->href( $url_domain , $new_url_base , $url_vars );

			# die( 'ahref: ' . $ahref );
			http_response_code( 307 );
			header( 'Location: ' . $ahref );
		}
		# print 'lang: ' . $lang . '<br />';
		# print 'lang cookie: ' . $lang_cookie . '<br />';


		# # SKIN
		if( ! empty( $_GET[ 'skin' ] ) ){
			$value = $_GET[ 'skin' ];
			$exp = 0;
			$path = '/';
			$domain = $url_allias[ 0 ];
			setcookie( 'skin' , $value , $exp , $path , $domain );

			$ahref = $library->link( )->href( $url_domain , $url_base , use_all_adress_data : false );
			header( 'Location: ' . $ahref , true );
		}
	}
}