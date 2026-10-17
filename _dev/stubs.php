<?PHP

# stubs.php — для PHPStan

if ( ! defined( 'PROJECT_ROOT' ) ) define( 'PROJECT_ROOT' , '' );
if ( ! defined( 'CS_ROOT'  ) ) define( 'CS_ROOT'  , '' );
if ( ! defined( 'CS_LIB'   ) ) define( 'CS_LIB'   , '' );
if ( ! defined( 'CS_CMD'   ) ) define( 'CS_CMD'   , '' );
if ( ! defined( 'CS_TPL'   ) ) define( 'CS_TPL'   , '' );
if ( ! defined( 'ADMIN_ROOT' ) ) define( 'ADMIN_ROOT' , '' );

function error ( string $title , string $message , string $file = '' , int $line = 0 ) : never {}
function error_file ( string $path , string $file , int $line ) : never {}
function warning_file ( string $path , string $file , int $line ) : void {}
function error_class ( string $classname , string $file , int $line ) : never {}