<?PHP

class BlockNew {

	public static function plan ( array $params ) : array {
		return [
			'name'   => $params[ 'name' ] ?? '' ,
			'domain' => $params[ 'domain' ] ?? 'damoony.ru' ,
		];
	}

	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		$name = (string) ( $params[ 'name' ] ?? '' );
		if ( $err = Validate::allias_name( $name ) ) {
			$r->step( 'Валидация имени' , false , $err );
			return $r;
		}
		$r->step( 'Валидация имени' , true , $name );
		$ctx?->step( 'Валидация имени' , true , $name );

		$domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
		if ( $err = Validate::domain( $domain ) ) {
			$r->step( 'Валидация домена' , false , $err );
			return $r;
		}

		$domain_path = Paths::domain( $domain );
		if ( ! is_dir( $domain_path ) ) {
			$r->error( "Домен не найден: {$domain_path}" );
			return $r;
		}

		$ns = self::get_domain_ns( $domain );
		if ( $ns === null ) {
			$r->error( "Не найден domains_namespace для {$domain}" );
			return $r;
		}
		$r->step( 'Namespace' , true , $ns );
		$ctx?->step( 'Namespace' , true , $ns );

		$block_path = $domain_path . '/alliasglobal/' . $name;
		if ( Fs::exists( $block_path ) ) {
			$r->error( "Блок уже существует: {$block_path}" );
			return $r;
		}

		# 1. создать файлы блока
		$vars = [
			'name'      => $name ,
			'domain'    => $domain ,
			'domain_ns' => $ns ,
		];

		$tpl_dir = CS_TPL . '/block';
		if ( ! is_dir( $tpl_dir ) ) {
			$r->error( "Шаблон блока не найден: {$tpl_dir}" );
			return $r;
		}

		foreach ( Fs::scan_recursive( $tpl_dir ) as $rel ) {
			$content = file_get_contents( $tpl_dir . '/' . $rel );
			if ( $content === false ) continue;

			$content = self::render( $content , $vars );

			$out_rel = preg_replace( '/\.tpl$/' , '' , $rel );
			$out = $block_path . '/' . str_replace( 'block' , $name , $out_rel );
			Fs::write( $out , $content );
			$r->file( $out );
		}
		$r->step( 'Создан блок' , true , $name );
		$ctx?->step( 'Создан блок' , true , $name );

		# 2. патч alliasglobal.php
		$ag_file = $domain_path . '/alliasglobal.php';
		if ( ! is_file( $ag_file ) ) {
			$r->error( "Не найден alliasglobal.php: {$ag_file}" );
			return $r;
		}

		$ag_content = file_get_contents( $ag_file );
		$ag_new = self::patch_alliasglobal( $ag_content , $name , $ns );

		if ( $ag_new === null ) {
			$r->warn( "alliasglobal.php не удалось пропатчить — добавь вручную" );
		} else {
			file_put_contents( $ag_file , $ag_new );
			$r->file( $ag_file );
			$r->step( 'alliasglobal.php пропатчен' , true , $name );
			$ctx?->step( 'alliasglobal.php пропатчен' , true , $name );
		}

		# 3. lint
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

	# Патч alliasglobal.php: property + include + new
	private static function patch_alliasglobal ( string $content , string $name , string $ns ) : ?string {
		# Уже есть?
		if ( strpos( $content , '$' . $name ) !== false ) return null;

		# 1. property — после последнего "public alliasglobal_* $*;"
		if ( preg_match_all( '/public\s+alliasglobal_[a-z_]+\s+\$[a-z_]+\s*;/' , $content , $m , PREG_OFFSET_CAPTURE ) ) {
			$last = end( $m[ 0 ] );
			$pos  = $last[ 1 ] + strlen( $last[ 0 ] );
			$content = substr( $content , 0 , $pos )
			         . "\n\tpublic alliasglobal_" . $name . " $" . $name . ";"
			         . substr( $content , $pos );
		}

		# 2. include в autoload
		if ( preg_match_all( "/\( include_once \\\$path = 'alliasglobal' \. DS \. '[a-z_]+' \. DS \. '[a-z_]+\.php' \) or error_file\( \\\$path , __FILE__ , __LINE__ \);/" , $content , $m , PREG_OFFSET_CAPTURE ) ) {
			$last = end( $m[ 0 ] );
			$pos  = $last[ 1 ] + strlen( $last[ 0 ] );
			$content = substr( $content , 0 , $pos )
			         . "\n\t\t( include_once \$path = 'alliasglobal' . DS . '" . $name . "' . DS . '" . $name . ".php' ) or error_file( \$path , __FILE__ , __LINE__ );"
			         . substr( $content , $pos );
		}

		# 3. new в init
		if ( preg_match_all( '/\$this->[a-z_]+\s*=\s*new\s+alliasglobal_[a-z_]+\s*\(\s*\$this\s*\)\s*;/' , $content , $m , PREG_OFFSET_CAPTURE ) ) {
			$last = end( $m[ 0 ] );
			$pos  = $last[ 1 ] + strlen( $last[ 0 ] );
			$content = substr( $content , 0 , $pos )
			         . "\n\t\t\$this->" . $name . " = new alliasglobal_" . $name . "( \$this );"
			         . substr( $content , $pos );
		}

		return $content;
	}
}