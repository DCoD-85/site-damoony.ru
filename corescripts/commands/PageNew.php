<?PHP

class PageNew {

	public static function plan ( array $params ) : array {
		return [
			'name'   => $params[ 'name' ]   ?? '' ,
			'parent' => $params[ 'parent' ] ?? '' ,
			'domain' => $params[ 'domain' ] ?? 'damoony.ru' ,
			'allias' => $params[ 'allias' ] ?? 'www' ,
		];
	}

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		# 1. имя страницы
		$name = (string) ( $params[ 'name' ] ?? '' );
		if ( $err = Validate::allias_name( $name ) ) {
			$r->step( 'Валидация имени' , false , $err );
			return $r;
		}
		$r->step( 'Валидация имени' , true , $name );

		# 2. домен / аллиас
		$domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
		$allias = (string) ( $params[ 'allias' ] ?? 'www' );

		if ( $err = Validate::domain( $domain ) ) {
			$r->step( 'Валидация домена' , false , $err );
			return $r;
		}
		if ( $err = Validate::allias_name( $allias ) ) {
			$r->step( 'Валидация аллиаса' , false , $err );
			return $r;
		}

		$allias_path = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain . '/allias/' . $allias;
		if ( ! is_dir( $allias_path ) ) {
			$r->error( "Аллиас не найден: {$allias_path}" );
			return $r;
		}
		$r->step( 'Домен / аллиас' , true , "{$domain} / {$allias}" );

		# 3. parent (новое)
		$parent = trim( (string) ( $params[ 'parent' ] ?? '' ) , '/' );
		if ( $parent !== '' ) {
			if ( ! preg_match( '#^[a-z][a-z0-9_]*(/[a-z][a-z0-9_]*)*$#' , $parent ) ) {
				$r->error( "Некорректный путь родителя: {$parent}" );
				return $r;
			}
		}

		# 4. namespace домена
		$ns = self::get_domain_ns( $domain );
		if ( $ns === null ) {
			$r->error( "Не найден domains_namespace для {$domain}" );
			return $r;
		}
		$r->step( 'Namespace' , true , $ns );

		# 5. пути
		$lang_dir    = $allias_path . '/algoritm/lang';
		$parent_dir  = $parent === '' ? $lang_dir : $lang_dir . '/' . $parent;
		$parent_ns   = $parent === '' ? ''        : str_replace( '/' , '_' , $parent );
		$full_ns     = $parent_ns === '' ? $name  : $parent_ns . '_' . $name;

		$page_dir    = $parent_dir . '/' . $name;
		$page_file   = $page_dir . '/algoritm_lang_' . $full_ns . '.php';
		$parent_file = $parent === ''
			? $lang_dir . '/algoritm_lang.php'
			: $parent_dir . '/algoritm_lang_' . $parent_ns . '.php';

		if ( ! is_dir( $parent_dir ) ) {
			$r->error( "Родительский каталог не найден: " . Fs::rel( $parent_dir ) );
			return $r;
		}
		if ( ! is_file( $parent_file ) ) {
			$r->error( "Родительский алгоритм не найден: " . Fs::rel( $parent_file ) );
			return $r;
		}
		if ( Fs::exists( $page_file ) ) {
			$r->error( "Страница уже существует: " . Fs::rel( $page_file ) );
			return $r;
		}

		# 6. создать .php + .data.json
		$tpl_php  = CS_TPL . '/page/page.php.tpl';
		$tpl_json = CS_TPL . '/page/page.data.json.tpl';

		if ( ! is_file( $tpl_php ) || ! is_file( $tpl_json ) ) {
			$r->error( 'Шаблоны не найдены в corescripts/templates/page/' );
			return $r;
		}

		$vars_render = [
			'name'      => $full_ns ,
			'UPPER'     => strtoupper( $full_ns ) ,
			'domain'    => $domain ,
			'domain_ns' => $ns ,
			'allias'    => $allias ,
		];

		Fs::write( $page_file , self::render( file_get_contents( $tpl_php ) , $vars_render ) );
		$r->file( $page_file );
		$r->step( 'Создан файл' , true , Fs::rel( $page_file ) );

		$json_file = $page_dir . '/algoritm_lang_' . $full_ns . '.data.json';
		Fs::write( $json_file , file_get_contents( $tpl_json ) );
		$r->file( $json_file );
		$r->step( 'Создан json' , true , Fs::rel( $json_file ) );

		# 7. patch $available — в родительский .data.json
		$parent_json = preg_replace( '/\.php$/' , '.data.json' , $parent_file );

		if ( ! is_file( $parent_json ) ) {
			$r->warn( 'Родительский .data.json не найден: ' . Fs::rel( $parent_json ) );
		} else {
			$patched = self::patch_available_json( $parent_json , $name );
			if ( $patched ) {
				$r->file( $parent_json );
				$r->step( 'Обновлён $available' , true , Fs::rel( $parent_json ) );
			} else {
				$r->warn( "'{$name}' уже в \$available" );
			}
		}

		# 8. lint
		$lint_errors = 0;
		foreach ( $r->created as $full ) {
			if ( substr( $full , -4 ) !== '.php' ) continue;
			if ( $err = Template::lint( $full ) ) {
				$r->warn( "Lint: " . Fs::rel( $full ) . ": {$err}" );
				$lint_errors++;
			}
		}
		if ( $lint_errors === 0 ) {
			$r->step( 'Синтаксис PHP' , true , 'все файлы OK' );
		}

		return $r;
	}

	private static function render ( string $content , array $vars ) : string {
		$search  = [ ];
		$replace = [ ];
		foreach ( $vars as $k => $v ) {
			$search[ ]  = '<' . $k . '>';
			$replace[ ] = (string) $v;
		}
		return str_replace( $search , $replace , $content );
	}

	private static function get_domain_ns ( string $domain ) : ?string {
		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
		} catch ( Throwable $e ) { return null; }
		$ns = $cfg->getKeys( [ 'domains_namespace' , $domain ] );
		return is_string( $ns ) && $ns !== '' ? $ns : null;
	}

	private static function patch_available_json ( string $file , string $name ) : bool {
		$raw = @file_get_contents( $file );
		if ( $raw === false ) return false;

		$data = @json_decode( $raw , true );
		if ( ! is_array( $data ) ) return false;

		if ( ! isset( $data[ 'default' ][ 'available' ] ) ) return false;
		if ( ! is_array( $data[ 'default' ][ 'available' ] ) ) return false;

		if ( in_array( $name , $data[ 'default' ][ 'available' ] , true ) ) return false;

		$data[ 'default' ][ 'available' ][ ] = $name;

		$out = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		$out = preg_replace_callback( '/^( +)/m' , fn( $m ) => str_repeat( "\t" , (int) ( strlen( $m[ 1 ] ) / 4 ) ) , $out );

		file_put_contents( $file , $out );
		return true;
	}
}