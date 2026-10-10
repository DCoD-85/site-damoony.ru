<?PHP

class Renderer {

	public static function cli ( Result $r ) : void {
		Cli::out( '' );

		if ( $r->ok ) {
			Cli::ok( 'Готово' );
		} else {
			Cli::err( 'Ошибка' );
		}

		if ( ! empty( $r->created ) ) {
			Cli::out( '' );
			Cli::info( 'Созданные файлы (' . count( $r->created ) . '):' );
			foreach ( $r->created as $f ) {
				Cli::out( '  ' . Fs::rel( $f ) );
			}
		}

		if ( ! empty( $r->warnings ) ) {
			Cli::out( '' );
			foreach ( $r->warnings as $w ) {
				Cli::warn( $w );
			}
		}

		if ( ! empty( $r->errors ) ) {
			Cli::out( '' );
			foreach ( $r->errors as $e ) {
				Cli::err( $e );
			}
		} elseif ( ! $r->ok && ! empty( $r->steps ) ) {
			# ошибка не в errors, а в шагах — показать последний неудачный
			Cli::out( '' );
			foreach ( array_reverse( $r->steps ) as $s ) {
				if ( ! $s[ 'ok' ] ) {
					$msg = $s[ 'msg' ] !== '' ? ' — ' . $s[ 'msg' ] : '';
					Cli::err( $s[ 'name' ] . $msg );
					break;
				}
			}
		}

		Cli::out( '' );
	}
}