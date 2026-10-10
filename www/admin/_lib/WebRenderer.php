<?PHP

class WebRenderer {

	public static function html ( Result $r ) : string {
		$h = '<div class="result ' . ( $r->ok ? 'ok' : 'err' ) . '">';

		$h .= '<h2>' . ( $r->ok ? '✓ Готово' : '✗ Ошибка' ) . '</h2>';

		if ( ! empty( $r->steps ) ) {
			$h .= '<ul class="steps">';
			foreach ( $r->steps as $s ) {
				$cls = $s[ 'ok' ] ? 'ok' : 'err';
				$msg = $s[ 'msg' ] !== '' ? ' — ' . htmlspecialchars( $s[ 'msg' ] ) : '';
				$h  .= '<li class="' . $cls . '">' . htmlspecialchars( $s[ 'name' ] ) . $msg . '</li>';
			}
			$h .= '</ul>';
		}

		if ( ! empty( $r->created ) ) {
			$h .= '<h3>Созданные файлы (' . count( $r->created ) . '):</h3>';
			$h .= '<ul class="files">';
			foreach ( $r->created as $f ) {
				$h .= '<li>' . htmlspecialchars( Fs::rel( $f ) ) . '</li>';
			}
			$h .= '</ul>';
		}

		if ( ! empty( $r->warnings ) ) {
			$h .= '<h3>Предупреждения:</h3><ul class="warnings">';
			foreach ( $r->warnings as $w ) $h .= '<li>' . htmlspecialchars( $w ) . '</li>';
			$h .= '</ul>';
		}

		if ( ! empty( $r->errors ) ) {
			$h .= '<h3>Ошибки:</h3><ul class="errors">';
			foreach ( $r->errors as $e ) $h .= '<li>' . htmlspecialchars( $e ) . '</li>';
			$h .= '</ul>';
		}

		$h .= '</div>';
		return $h;
	}
}