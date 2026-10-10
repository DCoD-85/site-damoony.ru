<?PHP

class PageNew {

	public static function plan ( array $params ) : array {
		return [
			'name'   => $params[ 'name' ] ?? '' ,
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
		$ctx?->step( 'Валидация имени' , true , $name );

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
		$ctx?->step( 'Домен / аллиас' , true , "{$domain} / {$allias}" );

		# 3. namespace домена
		$ns = self::get_domain_ns( $domain );
		if ( $ns === null ) {
			$r->error( "Не найден domains_namespace для {$domain} в site.config" );
			return $r;
		}
		$r->step( 'Namespace' , true , $ns );
		$ctx?->step( 'Namespace' , true , $ns );

		# 4. путь страницы
		$lang_dir = $allias_path . '/algoritm/lang';
		$page_dir = $lang_dir . '/' . $name;
		$page_file = $page_dir . '/algoritm_lang_' . $name . '.php';
		$lang_file = $lang_dir . '/algoritm_lang.php';

		if ( ! is_dir( $lang_dir ) ) {
			$r->error( "Не найден каталог lang: {$lang_dir}" );
			return $r;
		}
		if ( Fs::exists( $page_file ) ) {
			$r->error( "Страница уже существует: {$page_file}" );
			return $r;
		}

		# 5. создать файл страницы
		$tpl = CS_TPL . '/page/page.php.tpl';
		if ( ! is_file( $tpl ) ) {
			$r->error( "Шаблон не найден: {$tpl}" );
			return $r;
		}

		$vars = [
			'name'      => $name ,
			'UPPER'     => strtoupper( $name ) ,
			'domain'    => $domain ,
			'domain_ns' => $ns ,
			'allias'    => $allias ,
		];

		$content = self::render( file_get_contents( $tpl ) , $vars );
		Fs::write( $page_file , $content );
		$r->file( $page_file );
		$r->step( 'Создан файл страницы' , true , Fs::rel( $page_file ) );
		$ctx?->step( 'Создан файл страницы' , true , Fs::rel( $page_file ) );

		# 6. добавить в $available
		if ( ! is_file( $lang_file ) ) {
			$r->warn( "Не найден {$lang_file} — $available не обновлён" );
		} else {
			$lang_content = file_get_contents( $lang_file );
			$new_content  = self::patch_available( $lang_content , $name );

			if ( $new_content === null ) {
				$r->warn( "'{$name}' уже в \$available или не найден \$available" );
			} else {
				file_put_contents( $lang_file , $new_content );
				$r->file( $lang_file );
				$r->step( 'Обновлён $available' , true , $name );
				$ctx?->step( 'Обновлён $available' , true , $name );
			}
		}

		# 7. lint
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
			$ctx?->step( 'Синтаксис PHP' , true , 'все файлы OK' );
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
		} catch ( Throwable $e ) {
			return null;
		}
		$ns = $cfg->getKeys( [ 'domains_namespace' , $domain ] );
		return is_string( $ns ) && $ns !== '' ? $ns : null;
	}

	private static function patch_available ( string $content , string $name ) : ?string {
		if ( strpos( $content , "'{$name}'" ) !== false ) return null;

		$patched = preg_replace_callback(
			'/(public\s+array\s+\$available\s*=\s*\[)([^\]]*)(\])/s' ,
			function ( $m ) use ( $name ) {
				$inner = rtrim( $m[ 2 ] , " \t\n\r," );
				return $m[ 1 ] . $inner . " , '{$name}'" . $m[ 3 ];
			} ,
			$content ,
			1
		);

		return $patched === $content ? null : $patched;
	}
}