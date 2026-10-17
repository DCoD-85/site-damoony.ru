<?PHP

if( ! defined( 'DS' ) )  define( 'DS'  , DIRECTORY_SEPARATOR );
if( ! defined( 'DSA' ) ) define( 'DSA' , '/' );

# Debug — по умолчанию всё выключено
if( ! defined( 'GL_LOG' ) )       define( 'GL_LOG'       , false );
if( ! defined( 'GL_DEBUG' ) )     define( 'GL_DEBUG'     , false );
if( ! defined( 'GL_DEBUGHARD' ) ) define( 'GL_DEBUGHARD' , false );

# Database limits — defaults (можно переопределить через GET/POST до DB_LIMIT_MAX)
if( ! defined( 'DB_LIMIT_ONE' ) )  define( 'DB_LIMIT_ONE'  , 1 );
if( ! defined( 'DB_LIMIT_PAGE' ) ) define( 'DB_LIMIT_PAGE' , 10 );
if( ! defined( 'DB_LIMIT_MAX' ) )  define( 'DB_LIMIT_MAX'  , 1000 );

if( ! defined( 'VISUOLINK_RAND_DEFAULT' ) ) define( 'VISUOLINK_RAND_DEFAULT' , true );

?>