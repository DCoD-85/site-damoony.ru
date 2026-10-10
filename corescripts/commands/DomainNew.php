<?PHP

class DomainNew {

	# --- план (dry-run) ---
	public static function plan ( array $params ) : array {
		$domain = $params[ 'name' ] ?? $params[ 'domain' ] ?? '';
		$ns     = $params[ 'namespace' ] ?? Validate::ns_from_domain( $domain );

		return [
			'domain'    => $domain ,
			'namespace' => $ns ,
			'path'      => 'www/logic/alpha.0.0/domain/' . $domain ,
			'templates' => Template::scan( CS_TPL . '/domain' ) ,
		];
	}

	# --- выполнение ---
	public static function run ( array $params , ?Context $ctx = null ) : Result {
		$r = new Result( );

		# 1. валидация
		$domain = (string) ( $params[ 'name' ] ?? $params[ 'domain' ] ?? '' );
		if ( $err = Validate::domain( $domain ) ) {
			$r->step( 'Валидация домена' , false , $err );
			return $r;
		}
		$r->step( 'Валидация домена' , true , $domain );
		$ctx?->step( 'Валидация домена' , true , $domain );

		$ns = (string) ( $params[ 'namespace' ] ?? Validate::ns_from_domain( $domain ) );
		if ( $err = Validate::namespace( $ns ) ) {
			$r->step( 'Валидация namespace' , false , $err );
			return $r;
		}
		$r->step( 'Валидация namespace' , true , $ns );
		$ctx?->step( 'Валидация namespace' , true , $ns );

		# 2. проверить целевую папку
		$src = CS_TPL . '/domain';
		$dst = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain;

		if ( ! is_dir( $src ) ) {
			$r->error( "Шаблон не найден: {$src}" );
			return $r;
		}
		if ( Fs::exists( $dst ) ) {
			$r->error( "Папка уже существует: {$dst}" );
			return $r;
		}

		# 3. собираем список файлов шаблона
		$files = Fs::scan_recursive( $src );
		if ( empty( $files ) ) {
			$r->error( 'Шаблон пуст' );
			return $r;
		}

		# 4. копируем с заменой плейсхолдеров
		$vars = [
			'domain'    => $domain ,
			'domain_ns' => $ns ,
		];

		foreach ( $files as $rel ) {
			$content = file_get_contents( $src . '/' . $rel );
			if ( $content === false ) {
				$r->error( "Не прочитать: {$rel}" );
				continue;
			}

			$content = str_replace(
				[ '<domain_ns>' , '<domain>' ] ,
				[ $ns , $domain ] ,
				$content
			);

			# .php.tpl → .php
			$out_rel = preg_replace( '/\.tpl$/' , '' , $rel );

			$out = $dst . '/' . $out_rel;
			Fs::write( $out , $content );
			$r->file( $out );
		}

		$r->step( 'Создано файлов' , true , (string) count( $r->created ) );
		$ctx?->step( 'Создано файлов' , true , (string) count( $r->created ) );
		
		# 4.5. создать папку media/<domain>/ из шаблона
		$media_path = PROJECT_ROOT . '/www/media/' . $domain;
		$media_src  = CS_TPL . '/media';

		if ( ! is_dir( $media_path ) ) {
			if ( is_dir( $media_src ) ) {
				Fs::copy_recursive( $media_src , $media_path );
				$r->step( 'Создана папка media из шаблона' , true , Fs::rel( $media_path ) );
				$ctx?->step( 'Создана папка media' , true , Fs::rel( $media_path ) );
			} else {
				Fs::mkdir( $media_path );
				$r->warn( "Шаблон media не найден ({$media_src}) — папка создана пустой" );
			}
		}

		# 5. lint
		$lint_errors = 0;
		foreach ( $r->created as $full ) {
			if ( substr( $full , -4 ) !== '.php' ) continue;
			if ( $err = Template::lint( $full ) ) {
				$r->warn( "Lint: " . Fs::rel( $full ) . ": {$err}" );
				$lint_errors++;
			}
		}

		if ( $lint_errors > 0 ) {
			$r->warn( "Файлов с ошибками синтаксиса: {$lint_errors}" );
		} else {
			$r->step( 'Синтаксис PHP' , true , 'все файлы OK' );
			$ctx?->step( 'Синтаксис PHP' , true , 'все файлы OK' );
		}

		# 6. регистрация в site.config (по флагу --register)
		if ( ! empty( $params[ 'register' ] ) ) {
			self::register( $r , $domain , $ns , $ctx );
		}

		return $r;
	}
	
	# ============================================================
	#   REGISTER — регистрация домена в site.config
	# ============================================================

	private static function register ( Result $r , string $domain , string $ns , ?Context $ctx = null ) : void {
		$path = PROJECT_ROOT . '/config/site.config';

		try {
			$cfg = new Config( $path );
		} catch ( Throwable $e ) {
			$r->error( "Не открыть site.config: " . $e->getMessage( ) );
			return;
		}

		# --- 1. domains_real ---
		if ( ! $cfg->hasKeys( [ 'domains_real' , $domain ] ) ) {
			$cfg->setKeys( [ 'domains_real' , $domain ] , $domain );
			$r->step( 'site.config: domains_real' , true , $domain );
			$ctx?->step( 'site.config: domains_real' , true , $domain );
		} else {
			$r->warn( "domains_real.{$domain} уже есть — пропускаю" );
		}

		# --- 2. domains_available ---
		if ( ! $cfg->hasKeys( [ 'domains_available' , $domain ] ) ) {
			$cfg->setKeys( [ 'domains_available' , $domain ] , true );
			$r->step( 'site.config: domains_available' , true , $domain );
			$ctx?->step( 'site.config: domains_available' , true , $domain );
		}

		# --- 3. domains_namespace ---
		if ( ! $cfg->hasKeys( [ 'domains_namespace' , $domain ] ) ) {
			$cfg->setKeys( [ 'domains_namespace' , $domain ] , $ns );
			$r->step( 'site.config: domains_namespace' , true , $ns );
			$ctx?->step( 'site.config: domains_namespace' , true , $ns );
		}

		# --- 4. alliases_real ---
		if ( ! $cfg->hasKeys( [ 'alliases_real' , $domain ] ) ) {
			$cfg->setKeys( [ 'alliases_real' , $domain ] , [
				''    => 'www' ,
				'www' => 'www' ,
			] );
			$r->step( 'site.config: alliases_real' , true , 'www' );
			$ctx?->step( 'site.config: alliases_real' , true , 'www' );
		}

		# --- 5. alliases_available ---
		if ( ! $cfg->hasKeys( [ 'alliases_available' , $domain ] ) ) {
			$cfg->setKeys( [ 'alliases_available' , $domain ] , [
				'www' => true ,
			] );
			$r->step( 'site.config: alliases_available' , true , 'www' );
			$ctx?->step( 'site.config: alliases_available' , true , 'www' );
		}

		# --- 6. fillers ---
		if ( ! $cfg->hasKeys( [ 'fillers' , $domain ] ) ) {
			$cfg->setKeys( [ 'fillers' , $domain ] , [
				'www' => [
					'top'    => false ,
					'left'   => false ,
					'center' => false ,
					'right'  => false ,
					'bottom' => false ,
					'policy' => false ,
					'debug'  => false ,
				] ,
			] );
			$r->step( 'site.config: fillers' , true , 'www' );
			$ctx?->step( 'site.config: fillers' , true , 'www' );
		}

		# --- save ---
		try {
			$cfg->save( );
			$r->step( 'site.config сохранён' , true , 'backup: site.config.bak' );
			$ctx?->step( 'site.config сохранён' , true , 'backup: site.config.bak' );
		} catch ( Throwable $e ) {
			$r->error( "Не сохранить site.config: " . $e->getMessage( ) );
		}
	}
}