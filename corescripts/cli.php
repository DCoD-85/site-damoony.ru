<?PHP

# ============================================================
#   corescripts/cli.php — CLI-точка входа
#   php corescripts/cli.php <command> [args]
# ============================================================

if ( PHP_SAPI !== 'cli' ) {
	die( "Only CLI mode allowed\n" );
}

define( 'CS_ROOT'      , __DIR__ );
define( 'CS_LIB'       , CS_ROOT . DIRECTORY_SEPARATOR . '_lib' );
define( 'CS_CMD'       , CS_ROOT . DIRECTORY_SEPARATOR . 'commands' );
define( 'CS_TPL'       , CS_ROOT . DIRECTORY_SEPARATOR . 'templates' );
define( 'PROJECT_ROOT' , dirname( CS_ROOT ) );

require_once CS_LIB . '/Cli.php';
require_once CS_LIB . '/Fs.php';
require_once CS_LIB . '/Json.php';
require_once CS_LIB . '/Paths.php';
require_once CS_LIB . '/Blocks.php';
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';
require_once CS_LIB . '/Renderer.php';

$argv    = $_SERVER[ 'argv' ] ?? [ ];
$command = $argv[ 1 ] ?? 'help';
$args    = array_slice( $argv , 2 );

# cmd => [ file , class ]
$map = [
	'domain:new'     => [ 'DomainNew.php'    , 'DomainNew'    ] ,
	'domain:list'    => [ 'DomainList.php'   , 'DomainList'   ] ,
	'domain:remove'  => [ 'DomainRemove.php' , 'DomainRemove' ] ,
	'allias:new'     => [ 'AlliasNew.php'    , 'AlliasNew'    ] ,
	'allias:list'    => [ 'AlliasList.php'   , 'AlliasList'   ] ,
	'allias:remove'  => [ 'AlliasRemove.php' , 'AlliasRemove' ] ,
	'page:new'       => [ 'PageNew.php'      , 'PageNew'      ] ,
	'block:new'      => [ 'BlockNew.php'     , 'BlockNew'     ] ,
	'filler:new'     => [ 'FillerNew.php'    , 'FillerNew'    ] ,
	'admin:passwd'   => [ 'AdminPasswd.php'  , 'AdminPasswd'  ] ,
	'settings:set'   => [ 'SettingsSet.php'  , 'SettingsSet'  ] ,
	'settings:list'  => [ 'SettingsList.php' , 'SettingsList' ] ,
	'vars:extract'   => [ 'VarsExtract.php'  , 'VarsExtract'  ] ,
];

if ( $command === 'help' || ! isset( $map[ $command ] ) ) {
	Cli::help( array_merge( [ 'help' ] , array_keys( $map ) ) );
	exit( 0 );
}

[ $file_name , $class ] = $map[ $command ];

$file = CS_CMD . DIRECTORY_SEPARATOR . $file_name;
if ( ! is_file( $file ) ) {
	Cli::err( "Command not implemented: {$command}" );
	exit( 1 );
}

require_once $file;

# --- парсинг аргументов ---
#   --key=value   → $params[key] = value
#   --flag        → $params[flag] = true
#   позиционные   → $params[positional][] + $params[name] (первый)
$params     = [ ];
$positional = [ ];

foreach ( $args as $a ) {
	if ( str_starts_with( $a , '--' ) ) {
		$kv = explode( '=' , substr( $a , 2 ) , 2 );
		$params[ $kv[ 0 ] ] = $kv[ 1 ] ?? true;
	} elseif ( $a === '-h' ) {
		Cli::out( "Usage: php corescripts/cli.php {$command} [--key=value] [--flag] [positional]" );
		exit( 0 );
	} else {
		$positional[ ] = $a;
	}
}

# первый позиционный — исторический $params['name']
if ( ! empty( $positional ) && ! isset( $params[ 'name' ] ) ) {
	$params[ 'name' ] = $positional[ 0 ];
}
if ( ! empty( $positional ) ) {
	$params[ 'positional' ] = $positional;
}

# --- обработчик шага → печатает сразу ---
$ctx = new Context( function ( string $name , bool $ok , string $msg ) {
	$ok ? Cli::ok( $name . ( $msg !== '' ? ' — ' . $msg : '' ) )
	    : Cli::err( $name . ( $msg !== '' ? ' — ' . $msg : '' ) );
} );

if ( ! class_exists( $class ) ) {
	Cli::err( "Class not found: {$class}" );
	exit( 1 );
}

try {
	$result = $class::run( $params , $ctx );
} catch ( Throwable $e ) {
	Cli::err( $e->getMessage( ) );
	exit( 2 );
}

Renderer::cli( $result );
exit( $result->ok ? 0 : 1 );