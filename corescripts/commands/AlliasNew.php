<?PHP

class AlliasNew {

	const BLOCKS = [ 'top' , 'left' , 'center' , 'right' , 'bottom' , 'policy' , 'debug' ];

	# --- план ---
	public static function plan ( array $params ) : array {
		$name   = $params[ 'name' ] ?? '';
		$domain = $params[ 'domain' ] ?? 'damoony.ru';

		return [
			'name'   => $name ,
			'domain' => $domain ,
			'path'   => "www/logic/alpha.0.0/domain/{$domain}/allias/{$name}" ,
		];
	}

	# --- выполнение ---
	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		# 1. валидация имени
		$name = (string) ( $params[ 'name' ] ?? '' );
		if ( $err = Validate::allias_name( $name ) ) {
			$r->step( 'Валидация имени' , false , $err );
			return $r;
		}
		$r->step( 'Валидация имени' , true , $name );
		$ctx?->step( 'Валидация имени' , true , $name );

		# 2. домен
		$domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
		if ( $err = Validate::domain( $domain ) ) {
			$r->step( 'Валидация домена' , false , $err );
			return $r;
		}
		$r->step( 'Валидация домена' , true , $domain );
		$ctx?->step( 'Валидация домена' , true , $domain );

		$domain_path = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain;
		if ( ! is_dir( $domain_path ) ) {
			$r->error( "Домен не найден: {$domain_path}" );
			return $r;
		}

		# 3. путь аллиаса
		$allias_path = $domain_path . '/allias/' . $name;
		if ( Fs::exists( $allias_path ) ) {
			$r->error( "Аллиас уже существует: {$allias_path}" );
			return $r;
		}

		# 4. получить domain_ns из site.config
		$ns = self::get_domain_ns( $domain );
		if ( $ns === null ) {
			$r->error( "Не найден domains_namespace для {$domain} в site.config" );
			return $r;
		}
		$r->step( 'Namespace домена' , true , $ns );
		$ctx?->step( 'Namespace домена' , true , $ns );

		# 5. копировать структуру аллиаса
		$src_allias = CS_TPL . '/allias';
		if ( ! is_dir( $src_allias ) ) {
			$r->error( "Шаблон аллиаса не найден: {$src_allias}" );
			return $r;
		}

		$vars = [
			'name'      => $name ,
			'Name'      => ucfirst( $name ) ,
			'domain'    => $domain ,
			'domain_ns' => $ns ,
		];

		$files = Fs::scan_recursive( $src_allias );
		foreach ( $files as $rel ) {
			$content = file_get_contents( $src_allias . '/' . $rel );
			if ( $content === false ) continue;

			$content = self::render( $content , $vars );

			$out_rel = preg_replace( '/\.tpl$/' , '' , $rel );
			$out = $allias_path . '/' . $out_rel;
			Fs::write( $out , $content );
			$r->file( $out );
		}

		$r->step( 'Структура аллиаса' , true , count( $files ) . ' файлов' );
		$ctx?->step( 'Структура аллиаса' , true , count( $files ) . ' файлов' );

		# 6. создать filler'ы для всех блоков
		$src_block       = CS_TPL . '/filler/block.php.tpl';
		$src_block_data  = CS_TPL . '/filler/block.data.php.tpl';

		if ( ! is_file( $src_block ) || ! is_file( $src_block_data ) ) {
			$r->warn( 'Шаблоны filler не найдены — пропускаю' );
		} else {
			foreach ( self::BLOCKS as $block ) {
				$block_dir = $domain_path . '/alliasglobal/' . $block;
				if ( ! is_dir( $block_dir ) ) {
					$r->warn( "Блок не найден: {$block}" );
					continue;
				}

				$vars_block = $vars + [ 'block' => $block ];

				# <block>_<name>.php
				$c = file_get_contents( $src_block );
				$c = self::render( $c , $vars_block );
				$out = $block_dir . '/' . $block . '_' . $name . '.php';
				Fs::write( $out , $c );
				$r->file( $out );

				# <block>_<name>.data.php
				$c = file_get_contents( $src_block_data );
				$c = self::render( $c , $vars_block );
				$out = $block_dir . '/' . $block . '_' . $name . '.data.php';
				Fs::write( $out , $c );
				$r->file( $out );
			}
			$r->step( 'Filler\'' . 'ы' , true , count( self::BLOCKS ) . ' блоков' );
			$ctx?->step( 'Filler\'' . 'ы' , true , count( self::BLOCKS ) . ' блоков' );
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

		# 8. регистрация
		if ( ! empty( $params[ 'register' ] ) ) {
			self::register( $r , $domain , $name , $ctx );
		}

		return $r;
	}

	# --- render ---
	private static function render ( string $content , array $vars ) : string {
		$search  = [ ];
		$replace = [ ];
		foreach ( $vars as $k => $v ) {
			$search[ ]  = '<' . $k . '>';
			$replace[ ] = (string) $v;
		}
		return str_replace( $search , $replace , $content );
	}

	# --- ns домена ---
	private static function get_domain_ns ( string $domain ) : ?string {
		try {
			$cfg = new Config( PROJECT_ROOT . '/config/site.config' );
		} catch ( Throwable $e ) {
			return null;
		}
		$ns = $cfg->getKeys( [ 'domains_namespace' , $domain ] );
		return is_string( $ns ) && $ns !== '' ? $ns : null;
	}

	# --- регистрация ---
	private static function register ( Result $r , string $domain , string $name , ?Context $ctx = null ) : void {
		$path = PROJECT_ROOT . '/config/site.config';

		try {
			$cfg = new Config( $path );
		} catch ( Throwable $e ) {
			$r->error( "Не открыть site.config: " . $e->getMessage( ) );
			return;
		}

		# alliases_real.<domain>.<name> = <name>
		if ( ! $cfg->hasKeys( [ 'alliases_real' , $domain , $name ] ) ) {
			$cfg->setKeys( [ 'alliases_real' , $domain , $name ] , $name );
			$r->step( 'site.config: alliases_real' , true , $name );
			$ctx?->step( 'site.config: alliases_real' , true , $name );
		}

		# alliases_available.<domain>.<name> = true
		if ( ! $cfg->hasKeys( [ 'alliases_available' , $domain , $name ] ) ) {
			$cfg->setKeys( [ 'alliases_available' , $domain , $name ] , true );
			$r->step( 'site.config: alliases_available' , true , $name );
			$ctx?->step( 'site.config: alliases_available' , true , $name );
		}

		# fillers.<domain>.<name> = все false
		if ( ! $cfg->hasKeys( [ 'fillers' , $domain , $name ] ) ) {
			$f = [ ];
			foreach ( self::BLOCKS as $b ) $f[ $b ] = false;
			$cfg->setKeys( [ 'fillers' , $domain , $name ] , $f );
			$r->step( 'site.config: fillers' , true , $name );
			$ctx?->step( 'site.config: fillers' , true , $name );
		}

		try {
			$cfg->save( );
			$r->step( 'site.config сохранён' , true , 'backup: site.config.bak' );
			$ctx?->step( 'site.config сохранён' , true , 'backup: site.config.bak' );
		} catch ( Throwable $e ) {
			$r->error( "Не сохранить site.config: " . $e->getMessage( ) );
		}
	}
}