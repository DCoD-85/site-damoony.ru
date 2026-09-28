<?PHP

namespace damoony;

class alliasglobal_headers {
	# vars
	public \main $main;
	# methods
	public function __construct( \main $main ) {
		$this->main = $main;
		$library = $main->library;
		$url = $library->url( );
		$link = $library->link( );

		$url_domain = $url->static_domain;
		$url_allias = $url->static_allias;
		$url_base = $url->static_base;
		$url_vars = $url->static_vars;
		


		if( $url_base[ 0 ] !== '' ){
			
			if( $_COOKIE[ 'language' ] !== $url_base[ 0 ] ){
				$value = $url_base[ 0 ];
				$exp = 0;
				$path = '/';
				$domain = $url_allias[ 0 ];
				# setcookie( 'language' , $value , $exp , $path , $domain );
				$value = 'ru-ru';
				# setcookie( 'lang' , $value , $exp , $path , $domain );
				# die( $url_base[ 0 ] );
				# header( 'Location: ' . $library->link( )->href( $url_domain , $url_base , $url_vars ) , true , 302 );
			}
			# header( 'Location: ' . $library->link( )->href( $url_domain , [ 'index' ] , $url_vars ) , true , 302 );
		}

		if( $_COOKIE[ 'language' ] == '' || $_COOKIE[ 'language' ] == 'ch' ){
			
			$value = 'ru-ru';
			$exp = 0;
			$path = '/';
			$domain = $url_allias[ 0 ];
			# setcookie( 'language' , $value , $exp , $path , $domain );
			$value = 'ru-ru';
			# setcookie( 'lang' , $value , $exp , $path , $domain );
			
			# $ahref = $library->link( )->href( $url_domain , $url_base , $url_vars );
			# header( 'Location: ' . $ahref , true , 302 );
		}
		
		$language = substr( $_GET[ 'language' ] , 0 , 2 );
		$lang = substr( $_GET[ 'language' ] , 0 , 5 );
		$skin = substr( $_GET[ 'skin' ] , 0 , 10 );
			

		if( $url_allias[ 0 ] ){
			if( $url_allias[ 1 ] !== $_COOKIE[ 'language' ] ){
				# # # setcookie( 'language' , $url_allias[ 1 ] );
				
				# $redirect_url = $_SERVER[ 'HTTP_REFERER' ];
				
				# 
				# setcookie( 'language' , $_GET[ 'language' ] );
				# $url_url = $url->url( );
				# $url_vars = $url->vars( );
				# $url_vars[ 'language' ] = false;
				# $ahref = $library->link( )->href( $url_url , $url_vars );
				# header( 'Location: ' . $ahref , true );
			}
		}
		
		if( $url_allias[ 1 ] ){
			if( $url_allias[ 1 ] !== $_COOKIE[ 'language' ] ){
				
				$value = 'ru';
				$exp = 0;
				$path = '/';
				$domain = $url_allias[ 0 ];
				# setcookie( 'language' , $value , $exp , $path , $domain );
				# header( 'Location: ' . $ahref , true , 302 );
				# # # setcookie( 'language' , $url_allias[ 1 ] );
				
				# $redirect_url = $_SERVER[ 'HTTP_REFERER' ];
				
				# 
				# setcookie( 'language' , $_GET[ 'language' ] );
				# $url_url = $url->url( );
				# $url_vars = $url->vars( );
				# $url_vars[ 'language' ] = false;
				# $ahref = $library->link( )->href( $url_url , $url_vars );
				# header( 'Location: ' . $ahref , true );
			}
		}
		
	}
	
	public function check ( ) {
		return true;
	}
}