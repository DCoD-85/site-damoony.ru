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
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';

# admin _lib
require_once ADMIN_ROOT . '/_lib/Auth.php';
require_once ADMIN_ROOT . '/_lib/View.php';
require_once ADMIN_ROOT . '/_lib/WebRenderer.php';

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

# --- dispatch ---
switch ( $p ) {

	case 'dashboard' :
		$cfg     = new Config( PROJECT_ROOT . '/config/site.config' );
		$domains = (array) $cfg->get( 'domains_real' );
		$rows    = [ ];
		foreach ( $domains as $d => $real ) {
			$alliases = [ ];
			foreach ( (array) $cfg->getKeys( [ 'alliases_real' , $d ] , [ ] ) as $a => $t ) {
				if ( $a === '' ) continue;
				$alliases[ ] = $a;
			}
			$rows[ ] = [
				'domain'   => $d ,
				'ns'       => $cfg->getKeys( [ 'domains_namespace' , $d ] ) ?? '—' ,
				'avail'    => ! empty( $cfg->getKeys( [ 'domains_available' , $d ] ) ) ,
				'fs'       => is_dir( PROJECT_ROOT . '/www/logic/alpha.0.0/domain/' . $d ) ,
				'alliases' => $alliases ,
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
			View::render( 'result' , [ 'result' => $result , 'title' => 'Создание домена' ] );
		} else {
			View::render( 'domain_new' , [ ] );
		}
		break;

	case 'allias:new' :
		$domains = array_keys( (array) ( new Config( PROJECT_ROOT . '/config/site.config' ) )->get( 'domains_real' ) );
		if ( $_SERVER[ 'REQUEST_METHOD' ] === 'POST' ) {
			require_once CS_CMD . '/AlliasNew.php';
			$result = AlliasNew::run( [
				'name'     => $_POST[ 'name' ]   ?? '' ,
				'domain'   => $_POST[ 'domain' ] ?? '' ,
				'register' => ! empty( $_POST[ 'register' ] ) ,
			] );
			View::render( 'result' , [ 'result' => $result , 'title' => 'Создание аллиаса' ] );
		} else {
			View::render( 'allias_new' , [ 'domains' => $domains ] );
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
				'result' => new Result( ) ,
				'title'  => 'Домен не найден' ,
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

			# skin: проверяем, что папка www/media/<domain>/skin/<skin> существует
			$skin_dir = PROJECT_ROOT . '/www/media/' . $domain . '/skin/' . $skin;
			if ( $skin !== '' && ! is_dir( $skin_dir ) ) {
				$errors[ ] = "Тема '{$skin}' не найдена в media/{$domain}/skin/";
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
			'skins' => Scan::skinsFor( $domain ) ,
		] );
		break;
	
	case 'allias:toggle' :

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$name   = (string) ( $_GET[ 'name' ]   ?? '' );
		$value  = (string) ( $_GET[ 'value' ]  ?? '' );  # '1' | '0'

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

		$domain = (string) ( $_GET[ 'domain' ] ?? '' );
		$value  = (string) ( $_GET[ 'value' ]  ?? '' );

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
					'result' => $result ,
					'title'  => 'Удаление домена: ' . $domain ,
				] );
				break;
			}

			View::render( 'domain_remove' , [
				'domain' => $domain ,
				'errors' => $errors ,
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
					'result' => $result ,
					'title'  => "Удаление аллиаса: {$domain} / {$name}" ,
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
		$lang_dir = PROJECT_ROOT . '/www/logic/' . $logic . '/domain/' . $domain . '/allias/' . $allias . '/algoritm/lang';

		$tree = is_dir( $lang_dir ) ? Scan::pageTree( $lang_dir ) : [ ];

		View::render( 'pages_tree' , [
			'domain' => $domain ,
			'allias' => $allias ,
			'logic'  => $logic ,
			'tree'   => $tree ,
			'has_dir' => is_dir( $lang_dir ) ,
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
			$parent = trim( (string) ( $_POST[ 'parent' ] ?? '' ) , '/' );

			require_once CS_CMD . '/PageNew.php';

			$result = PageNew::run( [
				'name'   => $name ,
				'parent' => $parent ,
				'domain' => $domain ,
				'allias' => $allias ,
			] );

			View::render( 'result' , [
				'result' => $result ,
				'title'  => "Создание страницы: {$domain} / {$allias} / {$name}" ,
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
				'result' => new Result( ) ,
				'title'  => 'Домен не указан' ,
			] );
			break;
		}

		$cfg  = new Config( PROJECT_ROOT . '/config/site.config' );
		$real = $cfg->getKeys( [ 'domains_real' , $domain ] );

		if ( $real === null ) {
			View::render( 'result' , [
				'result' => new Result( ) ,
				'title'  => "Домен не найден: {$domain}" ,
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
				'result' => $result ,
				'title'  => 'Настройки — ошибка' ,
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