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

		return $r;
	}
}