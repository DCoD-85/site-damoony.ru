<?PHP

class View {

	public static function render ( string $view , array $data = [ ] ) : void {
		$view_file = ADMIN_ROOT . '/views/' . $view . '.php';
		if ( ! is_file( $view_file ) ) {
			http_response_code( 500 );
			echo 'View not found: ' . htmlspecialchars( $view );
			return;
		}
		extract( $data , EXTR_SKIP );

		# мета-данные по умолчанию (шапка/крошки/кнопки)
		if ( ! isset( $meta ) || ! is_array( $meta ) ) {
			$meta = [
				'title'       => null ,
				'breadcrumbs' => [] ,
				'actions'     => '' ,
				'tabs'        => [] ,
			];
		}

		$layout = ADMIN_ROOT . '/views/_layout.php';
		include $layout;
	}
}
