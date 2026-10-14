<?PHP

# ============================================================
#   www/admin/index.php — точка входа админки
#   Изолирована от main.php / visuo. Подключает только
#   corescripts для доступа к commands и _lib.
# ============================================================

session_name( 'admin_sid' );

session_set_cookie_params( [
	'lifetime' => 0 ,
	'path'     => '/' ,
	'domain'   => '' ,
	'secure'   => ( ( $_SERVER[ 'HTTPS' ] ?? '' ) === 'on' ) ,
	'httponly' => true ,
	'samesite' => 'Lax' ,
] );

session_start( );

define( 'ADMIN_ROOT'   , __DIR__ );
define( 'PROJECT_ROOT' , dirname( dirname( ADMIN_ROOT ) ) );

$cs = PROJECT_ROOT . '/corescripts';

define( 'CS_ROOT' , $cs );
define( 'CS_LIB'  , $cs . '/_lib' );
define( 'CS_CMD'  , $cs . '/commands' );
define( 'CS_TPL'  , $cs . '/templates' );

# corescripts _lib
require_once CS_LIB . '/Fs.php';
require_once CS_LIB . '/Json.php';
require_once CS_LIB . '/Paths.php';
require_once CS_LIB . '/Blocks.php';
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';

# admin _lib
require_once ADMIN_ROOT . '/_lib/Auth.php';
require_once ADMIN_ROOT . '/_lib/View.php';
require_once ADMIN_ROOT . '/_lib/WebRenderer.php';
require_once ADMIN_ROOT . '/_lib/Csrf.php';

$auth = new Auth( );

$p = (string) ( $_GET[ 'p' ] ?? 'dashboard' );

# --- login / logout ---
if ( $p === 'login' ) {
	$error = null;
	if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
		$login = (string) ( $_POST[ 'login' ]    ?? '' );
		$pass  = (string) ( $_POST[ 'password' ] ?? '' );
		if ( $auth->login( $login , $pass ) ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}
		$error = 'Неверный логин или пароль';
	}
	View::render( 'login' , [ 'error' => $error ] );
	exit;
}

if ( $p === 'logout' ) {
	$auth->logout( );
	header( 'Location: ?p=login' );
	exit;
}

# --- защита ---
if ( ! $auth->check( ) ) {
	header( 'Location: ?p=login' );
	exit;
}

# --- CSRF guard для всех POST (кроме login) ---
if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' && ! Csrf::check( ) ) {
	http_response_code( 403 );
	exit( 'CSRF token invalid' );
}

# --- dispatch ---
switch ( $p ) {

	case 'dashboard' :
		$cfg     = new Config( PROJECT_ROOT . '/config/site.config' );
		$domains = (array) $cfg->get( 'domains_real' );

		# Собрать real-домены и их алиасы
		$real_domains = [ ];
		$aliases      = [ ];

		foreach ( $domains as $d => $real ) {
			if ( $d === $real ) {
				$real_domains[ $d ] = true;
			} else {
				$aliases[ $real ][ ] = $d;
			}
		}

		$rows = [ ];
		foreach ( array_keys( $real_domains ) as $real ) {
			$alliases = [ ];
			foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $real ] , [ ] ) as $a => $t ) {
				if ( $a === '' ) continue;
				$alliases[ ] = $a;
			}

			$rows[ ] = [
				'domain'   => $real ,
				'ns'       => $cfg->getKeys( [ 'domains_namespace' , $real ] ) ?? '—' ,
				'avail'    => ! empty( $cfg->getKeys( [ 'domains_available' , $real ] ) ) ,
				'fs'       => is_dir( Paths::domain( $real ) ) ,
				'alliases' => $alliases ,
				'aliases'  => $aliases[ $real ] ?? [ ] ,
			];
		}

		View::render( 'dashboard' , [ 'domains' => $rows ] );
		break;
	case 'domain:new' :
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/DomainNew.php';
			$result = DomainNew::run( [
				'name'     => $_POST[ 'domain' ] ?? '' ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
			] );
			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Создание домена' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
		} else {
			View::render( 'domain_new' , [ ] );
		}
		break;

	case 'allias:new' :
		$cfg         = new Config( Paths::config() );
		$domains_raw = (array) $cfg->get( 'domains_real' );

		# только real-домены (key === value): аллиасы принадлежат real
		$domains = [ ];
		foreach ( $domains_raw as $d => $real ) {
			if ( $d === $real ) $domains[ ] = $d;
		}

		$prefill_domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/AlliasNew.php';
			$post_domain = (string) ( $_POST[ 'domain' ] ?? '' );
			$result = AlliasNew::run( [
				'name'     => $_POST[ 'name' ]   ?? '' ,
				'domain'   => $post_domain ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
			] );
			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Создание аллиаса' ,
				'back_url'   => '?p=allias:list&domain=' . urlencode( $post_domain ) ,
				'back_label' => 'К аллиасам' ,
			] );
		} else {
			View::render( 'allias_new' , [
				'domains'        => $domains ,
				'prefill_domain' => $prefill_domain ,
			] );
		}
		break;

	case 'domain:edit' :

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		$cfg_path = PROJECT_ROOT . '/config/site.config';
		$cfg      = new Config( $cfg_path );

		# --- валидация домена ---
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );
		if ( $real === null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Домен не найден' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$title = trim( (string) ( $_POST[ 'title' ] ?? '' ) );
			$skin  = trim( (string) ( $_POST[ 'skin' ]  ?? '' ) );

			$errors = [ ];

			if ( $title === '' ) {
				$errors[ ] = 'Title не может быть пустым';
			}

			# skin: валидация имени + проверка папки
			if ( $skin !== '' ) {
				$skin_err = Validate::skin( $skin );
				if ( $skin_err !== null ) {
					$errors[ ] = $skin_err;
				} else {
					$skin_dir = PROJECT_ROOT . '/www/media/' . $domain . '/skin/' . $skin;
					if ( ! is_dir( $skin_dir ) ) {
						$errors[ ] = "Тема '{$skin}' не найдена в media/{$domain}/skin/";
					}
				}
			}

			if ( empty( $errors ) ) {
				try {
					$cfg->setKeys( [ 'domains_titles' , $domain ] , $title );
					if ( $skin !== '' ) {
						$cfg->setKeys( [ 'domains_skins' , $domain ] , $skin );
					}
					$cfg->save( );
					header( 'Location: ?p=domain:edit&domain=' . urlencode( $domain ) . '&saved=1' );
					exit;
				} catch ( Throwable $e ) {
					$errors[ ] = 'Не сохранить: ' . $e->getMessage( );
				}
			}

			# ошибки — показать форму заново с введённым
			View::render( 'domain_edit' , [
				'domain'  => $domain ,
				'saved'   => false ,
				'errors'  => $errors ,
				'current' => [ 'title' => $title , 'skin' => $skin ] ,
				'skins'   => Scan::skinsFor( $domain ) ,
			] );
			break;
		}

		# --- GET ---
		$title = (string) ( $cfg->getKeys( [ 'domains_titles' , $domain ] ) ?? $cfg->get( 'title' , '' ) );
		$skin  = (string) ( $cfg->getKeys( [ 'domains_skins'  , $domain ] ) ?? $cfg->get( 'skin'  , 'default' ) );

		View::render( 'domain_edit' , [
			'domain'  => $domain ,
			'saved'   => ! empty( $_GET[ 'saved' ] ) ,
			'errors'  => [ ] ,
			'current' => [ 'title' => $title , 'skin' => $skin ] ,
			'skins'   => Scan::skinsFor( $domain ) ,
		] );
		break;

	case 'allias:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$name   = (string) ( $_POST[ 'name' ]   ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' || $name === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$flag = ( $value === '0' ) ? false : true;
		$cfg->setKeys( [ 'alliases_available' , $domain , $name ] , $flag );
		$cfg->save( );

		header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) );
		exit;

	case 'domain:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$flag = ( $value === '0' ) ? false : true;
		$cfg->setKeys( [ 'domains_available' , $domain ] , $flag );
		$cfg->save( );

		header( 'Location: ?p=dashboard' );
		exit;

	case 'domain:remove' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$confirm_name    = trim( (string) ( $_POST[ 'confirm_name' ]    ?? '' ) );
			$confirm_checked = ! empty( $_POST[ 'confirm_checked' ] );

			$errors = [ ];

			if ( ! $confirm_checked ) {
				$errors[ ] = 'Подтвердите, что понимаете необратимость';
			}
			if ( $confirm_name !== $domain ) {
				$errors[ ] = 'Имя домена введено неверно';
			}

			if ( empty( $errors ) ) {
				require_once CS_CMD . '/DomainRemove.php';

				$result = DomainRemove::run( [
					'name'  => $domain ,
					'force' => true ,
				] );

				View::render( 'result' , [
					'result'     => $result ,
					'title'      => 'Удаление домена: ' . $domain ,
					'back_url'   => '?p=dashboard' ,
					'back_label' => 'К списку домен' ,
				] );
				break;
			}

			$alliases = [ ];
			foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $domain ] , [ ] ) as $a => $t ) {
				if ( $a === '' ) continue;
				$alliases[ ] = $a;
			}

			View::render( 'domain_remove' , [
				'domain'   => $domain ,
				'errors'   => $errors ,
				'alliases' => $alliases ,
			] );
			break;
		}

		# --- GET ---
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $domain ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		View::render( 'domain_remove' , [
			'domain'   => $domain ,
			'errors'   => [ ] ,
			'alliases' => $alliases ,
		] );
		break;

	case 'allias:remove' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$name   = (string) ( $_GET[ 'name' ]   ?? '' );

		if ( $domain === '' || $name === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg = new Config( PROJECT_ROOT . '/config/site.config' );

		$real = $cfg->getKeys( [ 'domains_real'    , $domain ] );
		$al   = $cfg->getKeys( [ 'alliases_real'   , $domain , $name ] );

		if ( $real === null || $al === null ) {
			header( 'Location: ?p=allias:list&domain=' . urlencode( $domain ) );
			exit;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$confirm_checked = ! empty( $_POST[ 'confirm_checked' ] );
			$confirm_name    = trim( (string) ( $_POST[ 'confirm_name' ] ?? '' ) );

			$errors = [ ];

			if ( ! $confirm_checked ) {
				$errors[ ] = 'Подтвердите, что понимаете необратимость';
			}
			if ( $confirm_name !== $name ) {
				$errors[ ] = 'Имя аллиаса введено неверно';
			}

			if ( empty( $errors ) ) {
				require_once CS_CMD . '/AlliasRemove.php';

				$result = AlliasRemove::run( [
					'name'   => $name ,
					'domain' => $domain ,
					'force'  => true ,
				] );

				View::render( 'result' , [
					'result'     => $result ,
					'title'      => "Удаление аллиаса: {$domain} / {$name}" ,
					'back_url'   => '?p=allias:list&domain=' . urlencode( $domain ) ,
					'back_label' => 'К аллиасам' ,
				] );
				break;
			}

			View::render( 'allias_remove' , [
				'domain' => $domain ,
				'name'   => $name ,
				'errors' => $errors ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'allias_remove' , [
			'domain' => $domain ,
			'name'   => $name ,
			'errors' => [ ] ,
		] );
		break;

	case 'pages:tree' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$logic    = (string) ( $cfg->get( 'logic' , 'alpha.0.0' ) ?? 'alpha.0.0' );
		$base = PROJECT_ROOT . '/www/logic/' . $logic . '/domain/' . $domain . '/allias/' . $allias . '/algoritm';
		$tree = is_dir( $base ) ? Scan::pageTree( $base ) : [ ];

		View::render( 'pages_tree' , [
			'domain'  => $domain ,
			'allias'  => $allias ,
			'logic'   => $logic ,
			'tree'    => $tree ,
			'has_dir' => is_dir( $base ) ,
		] );
		break;

	case 'page:edit' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );
		$path = trim( (string) ( $_GET[ 'path' ] ?? '' ) , '/' );

		$path_err = Validate::page_path( $path );
		if ( $path_err !== null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Некорректный путь: ' . $path_err ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$logic = (string) ( $cfg->get( 'logic' , 'alpha.0.0' ) ?? 'alpha.0.0' );
		$base  = PROJECT_ROOT . '/www/logic/' . $logic . '/domain/' . $domain . '/allias/' . $allias . '/algoritm';

		# --- вычисляем файлы: path относительно algoritm/ ---
		$cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) );
		$sub_dir   = $path === '' ? $base : $base . '/' . $path;
		$php_file  = $sub_dir . '/' . $cls_short . '.php';
		$json_file = $sub_dir . '/' . $cls_short . '.data.json';

		if ( ! is_file( $php_file ) ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => "Страница не найдена: {$path}" ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$available_raw = (string) ( $_POST[ 'available' ] ?? '' );
			$auth          = ! empty( $_POST[ 'auth' ] );
			$search        = ! empty( $_POST[ 'search' ] );
			$default       = ! empty( $_POST[ 'default' ] );

			# разбить на строки + trim + дедупликация
			$lines     = preg_split( '/\r\n|\n|\r/' , $available_raw );
			$available = [ ];
			$seen      = [ ];

			foreach ( $lines as $line ) {
				$line = trim( $line );

				if ( isset( $seen[ $line ] ) ) continue;
				$seen[ $line ] = true;

				$available[ ] = $line;
			}

			# убрать только trailing пустые (артефакт textarea)
			while ( ! empty( $available ) && end( $available ) === '' ) {
				array_pop( $available );
			}

			# если после чистки вообще ничего нет — оставить ['']
			if ( empty( $available ) ) {
				$available = [ '' ];
			}

			$data = [
				'default' => [
					'available' => $available ,
					'auth'      => $auth ,
					'search'    => $search ,
					'default'   => $default ,
				] ,
			];

			$out = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			$out = preg_replace_callback( '/^( +)/m' , fn( $m ) => str_repeat( "\t" , (int) ( strlen( $m[ 1 ] ) / 4 ) ) , $out );

			file_put_contents( $json_file , $out );

			header( 'Location: ?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path ) . '&saved=1' );
			exit;
		}

		# --- GET ---
		$current = [
			'available' => [ ] ,
			'auth'      => false ,
			'search'    => true ,
			'default'   => false ,
		];

		if ( is_file( $json_file ) ) {
			$json = @json_decode( @file_get_contents( $json_file ) , true );
			if ( is_array( $json ) && isset( $json[ 'default' ] ) && is_array( $json[ 'default' ] ) ) {
				$d = $json[ 'default' ];
				$current[ 'available' ] = isset( $d[ 'available' ] ) && is_array( $d[ 'available' ] ) ? $d[ 'available' ] : [ ];
				$current[ 'auth' ]      = ! empty( $d[ 'auth' ] );
				$current[ 'search' ]    = ! isset( $d[ 'search' ] ) || ! empty( $d[ 'search' ] );
				$current[ 'default' ]   = ! empty( $d[ 'default' ] );
			}
		}

		View::render( 'page_edit' , [
			'domain'    => $domain ,
			'allias'    => $allias ,
			'path'      => $path ,
			'cls_short' => $cls_short ,
			'json_file' => $json_file ,
			'current'   => $current ,
			'saved'     => ! empty( $_GET[ 'saved' ] ) ,
		] );
		break;

	case 'page:new' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		# список аллиасов домена
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $domain ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		# --- POST ---
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$allias = (string) ( $_POST[ 'allias' ] ?? '' );
			$name   = trim( (string) ( $_POST[ 'name' ] ?? '' ) );
			$parent   = trim( (string) ( $_POST[ 'parent' ] ?? '' ) , '/' );
			$path_err = Validate::page_path( $parent );
			if ( $path_err !== null ) {
				View::render( 'page_new' , [
					'domain'   => $domain ,
					'allias'   => $allias ,
					'parent'   => $parent ,
					'alliases' => $alliases ,
					'error'    => $path_err ,
				] );
				break;
			}

			require_once CS_CMD . '/PageNew.php';

			$result = PageNew::run( [
				'name'   => $name ,
				'parent' => $parent ,
				'domain' => $domain ,
				'allias' => $allias ,
			] );

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => "Создание страницы: {$domain} / {$allias} / {$name}" ,
				'back_url'   => '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) ,
				'back_label' => 'К страницам' ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'page_new' , [
			'domain'   => $domain ,
			'allias'   => $allias ,
			'parent'   => (string) ( $_GET[ 'parent' ] ?? '' ) ,
			'alliases' => $alliases ,
		] );
		break;

	case 'allias:list' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );

		if ( $domain === '' ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => 'Домен не указан' ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			View::render( 'result' , [
				'result'     => new Result( ) ,
				'title'      => "Домен не найден: {$domain}" ,
				'back_url'   => '?p=dashboard' ,
				'back_label' => 'К списку домен' ,
			] );
			break;
		}

		$logic     = (string) ( $cfg->get( 'logic' , 'alpha.0.0' ) ?? 'alpha.0.0' );
		$alliases  = (array)  $cfg->getKeys( [ 'alliases_real'      , $domain ] , [ ] );
		$available = (array)  $cfg->getKeys( [ 'alliases_available' , $domain ] , [ ] );
		$fillers   = (array)  $cfg->getKeys( [ 'fillers'            , $domain ] , [ ] );

		$rows = [ ];
		foreach ( $alliases as $alias => $target ) {
			if ( $alias === '' ) continue;

			$fs_path = PROJECT_ROOT . '/www/logic/' . $logic . '/domain/' . $domain . '/allias/' . $alias;
			$f       = (array) ( $fillers[ $alias ] ?? [ ] );

			$enabled = 0;
			foreach ( $f as $v ) if ( $v ) $enabled++;

			$rows[ ] = [
				'name'            => $alias ,
				'avail'           => ! empty( $available[ $alias ] ) ,
				'fs'              => is_dir( $fs_path ) ,
				'fillers_enabled' => $enabled ,
				'fillers_total'   => count( $f ) ,
			];
		}

		View::render( 'allias_list' , [
			'domain' => $domain ,
			'rows'   => $rows ,
		] );
		break;

	case 'page:toggle' :

		if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
			http_response_code( 405 );
			exit( 'Method not allowed' );
		}

		$domain = (string) ( $_POST[ 'domain' ] ?? '' );
		$allias = (string) ( $_POST[ 'allias' ] ?? '' );
		$path   = trim( (string) ( $_POST[ 'path' ] ?? '' ) , '/' );
		$key    = (string) ( $_POST[ 'key' ]    ?? '' );
		$value  = (string) ( $_POST[ 'value' ]  ?? '' );

		if ( $domain === '' || $allias === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		if ( ! in_array( $key , [ 'auth' , 'search' , 'default' ] , true ) ) {
			header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
			exit;
		}

		# защита от path traversal (единая валидация)
		if ( Validate::page_path( $path ) !== null ) {
			header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$logic     = (string) ( $cfg->get( 'logic' , 'alpha.0.0' ) ?? 'alpha.0.0' );
		$base      = PROJECT_ROOT . '/www/logic/' . $logic . '/domain/' . $domain . '/allias/' . $allias . '/algoritm';
		$cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) );
		$sub_dir   = $path === '' ? $base : $base . '/' . $path;
		$php_file  = $sub_dir . '/' . $cls_short . '.php';
		$json_file = $sub_dir . '/' . $cls_short . '.data.json';

		if ( ! is_file( $php_file ) ) {
			header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
			exit;
		}

		# читаем текущий .data.json (или дефолт)
		$data = [ 'default' => [
			'available' => [ '' ] ,
			'auth'      => false ,
			'search'    => true ,
			'default'   => false ,
		] ];

		if ( is_file( $json_file ) ) {
			$raw = @file_get_contents( $json_file );
			$cur = @json_decode( $raw , true );
			if ( is_array( $cur ) && isset( $cur[ 'default' ] ) && is_array( $cur[ 'default' ] ) ) {
				$data[ 'default' ] = array_merge( $data[ 'default' ] , $cur[ 'default' ] );
			}
		}

		$data[ 'default' ][ $key ] = ( $value === '0' ) ? false : true;

		$out = json_encode( $data , JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		$out = preg_replace_callback( '/^( +)/m' , fn( $m ) => str_repeat( "\t" , (int) ( strlen( $m[ 1 ] ) / 4 ) ) , $out );
		file_put_contents( $json_file , $out );

		header( 'Location: ?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) );
		exit;

	case 'filler:new' :

		require_once ADMIN_ROOT . '/_lib/Scan.php';

		$domain = (string) ( $_GET[ 'domain' ] ?? $_POST[ 'domain' ] ?? '' );
		$allias = (string) ( $_GET[ 'allias' ] ?? $_POST[ 'allias' ] ?? '' );

		if ( $domain === '' ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			header( 'Location: ?p=dashboard' );
			exit;
		}

		# список аллиасов домена
		$alliases = [ ];
		foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $domain ] , [ ] ) as $a => $t ) {
			if ( $a === '' ) continue;
			$alliases[ ] = $a;
		}

		$blocks = Scan::blocksFor( $domain );

		# --- POST ---
				if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {

			$allias = (string) ( $_POST[ 'allias' ] ?? '' );
			$block  = (string) ( $_POST[ 'block' ]  ?? '' );
			$enable = ! empty( $_POST[ 'enable' ] );

			$result = new Result( );

			# путь к будущему filler'у
			$block_path = PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $domain . '/alliasglobal/' . $block;
			$filler_php = $block_path . '/' . $block . '_' . $allias . '.php';

			if ( is_file( $filler_php ) ) {
				# уже есть — просто отметить
				$result->step( 'Filler уже существует' , true , Fs::rel( $filler_php ) );
			} else {
				require_once CS_CMD . '/FillerNew.php';
				$result = FillerNew::run( [
					'name'   => $allias ,
					'block'  => $block ,
					'domain' => $domain ,
					'allias' => $allias ,
				] );
			}

			# включить в конфиге, если попросили
			if ( $result->ok && $enable ) {
				try {
					$cur = $cfg->getKeys( [ 'fillers' , $domain , $allias , $block ] );
					if ( $cur === true ) {
						$result->step( 'Уже включено в site.config' , true , "fillers[{$domain}][{$allias}][{$block}]" );
					} else {
						$cfg->setKeys( [ 'fillers' , $domain , $allias , $block ] , true );
						$cfg->save( );
						$result->step( 'Включено в site.config' , true , "fillers[{$domain}][{$allias}][{$block}] = true" );
					}
				} catch ( Throwable $e ) {
					$result->warn( 'Не удалось включить в конфиге: ' . $e->getMessage( ) );
				}
			}

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => "Filler: {$domain} / {$allias} / {$block}" ,
				'back_url'   => '?p=allias:list&domain=' . urlencode( $domain ) ,
				'back_label' => 'К аллиасам' ,
			] );
			break;
		}

		# --- GET ---
		View::render( 'filler_new' , [
			'domain'   => $domain ,
			'allias'   => $allias ,
			'alliases' => $alliases ,
			'blocks'   => $blocks ,
		] );
		break;
	
	case 'settings' :
		require_once ADMIN_ROOT . '/_lib/Scan.php';

		# SettingsSet падает на неизвестном ключе → фильтруем $_POST
		$allowed = [ 'logic' , 'connect_type' ];

		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/SettingsSet.php';

			$params = [ ];
			foreach ( $allowed as $k ) {
				if ( array_key_exists( $k , $_POST ) ) {
					$params[ $k ] = $_POST[ $k ];
				}
			}

			$result = SettingsSet::run( $params );

			if ( $result->ok ) {
				header( 'Location: ?p=settings&saved=1' );
				exit;
			}

			View::render( 'result' , [
				'result'     => $result ,
				'title'      => 'Настройки — ошибка' ,
				'back_url'   => '?p=settings' ,
				'back_label' => 'К настройкам' ,
			] );
			break;
		}

		# GET
		$cfg = new Config( PROJECT_ROOT . '/config/site.config' );

		View::render( 'settings' , [
			'title'        => 'Настройки' ,
			'saved'        => ! empty( $_GET[ 'saved' ] ) ,
			'current'      => [
				'logic'        => (string) ( $cfg->get( 'logic' )        ?? '' ) ,
				'connect_type' => (string) ( $cfg->get( 'connect_type' ) ?? 'https://' ) ,
			] ,
			'logics'       => Scan::logics( ) ,
			'connectTypes' => Scan::connectTypes( ) ,
		] );
		break;

	default :
		http_response_code( 404 );
		echo '<h1>404</h1><p>Страница не найдена: ' . htmlspecialchars( $p ) . '</p>';
}