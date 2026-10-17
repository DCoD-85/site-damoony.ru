<?PHP

# ============================================================
#   stubs.php — определения для PHPStan.
#   Функции из www/index.php, используемые движком.
#   error/error_file/error_class не возвращают управление (die внутри).
# ============================================================

function error ( string $title , string $message , string $file = '' , string $line = '' ) : never {}
function error_file ( string $path , string $file , string $line ) : never {}
function warning_file ( string $path , string $file , string $line ) : void {}
function error_class ( string $classname , string $file , string $line ) : never {}