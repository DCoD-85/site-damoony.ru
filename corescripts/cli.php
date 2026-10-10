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
require_once CS_LIB . '/Template.php';
require_once CS_LIB . '/Config.php';
require_once CS_LIB . '/Validate.php';
require_once CS_LIB . '/Result.php';
require_once CS_LIB . '/Context.php';
require_once CS_LIB . '/Renderer.php';

$argv    = $_SERVER[ 'argv' ] ?? [ ];
$command = $argv[ 1 ] ?? 'help';
$args    = array_slice( $argv , 2 );

$map = [
	'help'           => null ,
	'domain:new'     => 'DomainNew.php' ,
	'domain:list'    => 'DomainList.php' ,
	'domain:remove'  => 'DomainRemove.php' ,
	'allias:new'     => 'AlliasNew.php' ,
	'allias:list'    => 'AlliasList.php' ,
	'allias:remove'  => 'AlliasRemove.php' ,
	'page:new'       => 'PageNew.php' ,
	'block:new'      => 'BlockNew.php' ,
	'filler:new'     => 'FillerNew.php' ,
	'admin:passwd'   => 'AdminPasswd.php' ,
	'settings:set'   => 'SettingsSet.php' ,
	'settings:list'  => 'SettingsList.php' ,
];

if ( $command === 'help' || ! array_key_exists( $command , $map ) ) {
	Cli::help( array_keys( $map ) );
	exit( 0 );
}

$file = CS_CMD . DIRECTORY_SEPARATOR . $map[ $command ];
if ( ! is_file( $file ) ) {
	Cli::err( "Command not implemented: {$command}" );
	exit( 1 );
}

require_once $file;

# --- парсинг аргументов: --key=value ---
$params = [ ];
foreach ( $args as $a ) {
	if ( str_starts_with( $a , '--' ) ) {
		$kv = explode( '=' , substr( $a , 2 ) , 2 );
		$params[ $kv[ 0 ] ] = $kv[ 1 ] ?? true;
	} else {
		$params[ 'name' ] = $a;
	}
}

# --- обработчик шага → печатает сразу ---
$ctx = new Context( function ( string $name , bool $ok , string $msg ) {
	$ok ? Cli::ok( $name . ( $msg !== '' ? ' — ' . $msg : '' ) )
	    : Cli::err( $name . ( $msg !== '' ? ' — ' . $msg : '' ) );
} );

# --- класс команды ---
$class_map = [
	'domain:new'     => 'DomainNew' ,
	'domain:list'    => 'DomainList' ,
	'domain:remove'  => 'DomainRemove' ,
	'allias:new'     => 'AlliasNew' ,
	'allias:list'    => 'AlliasList' ,
	'allias:remove'  => 'AlliasRemove' ,
	'page:new'       => 'PageNew' ,
	'block:new'      => 'BlockNew' ,
	'filler:new'     => 'FillerNew' ,
	'admin:passwd'   => 'AdminPasswd' ,
	'settings:set'   => 'SettingsSet' ,
	'settings:list'  => 'SettingsList' ,
];
$class = $class_map[ $command ];

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