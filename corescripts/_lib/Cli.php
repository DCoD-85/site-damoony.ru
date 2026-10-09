<?PHP

class Cli {

	public static function out ( string $msg ) : void {
		fwrite( STDOUT , $msg . "\n" );
	}

	public static function ok ( string $msg ) : void {
		fwrite( STDOUT , "\033[32m✔\033[0m " . $msg . "\n" );
	}

	public static function warn ( string $msg ) : void {
		fwrite( STDERR , "\033[33m⚠\033[0m " . $msg . "\n" );
	}

	public static function err ( string $msg ) : void {
		fwrite( STDERR , "\033[31m✘\033[0m " . $msg . "\n" );
	}

	public static function info ( string $msg ) : void {
		fwrite( STDOUT , "\033[36mℹ\033[0m " . $msg . "\n" );
	}

	public static function ask ( string $prompt , string $default = '' ) : string {
		$hint = $default !== '' ? " [{$default}]" : '';
		fwrite( STDOUT , $prompt . $hint . ': ' );
		$line = fgets( STDIN );
		if ( $line === false ) return $default;
		$line = trim( $line );
		return $line === '' ? $default : $line;
	}

	public static function confirm ( string $prompt , bool $default = true ) : bool {
		$hint = $default ? 'Y/n' : 'y/N';
		fwrite( STDOUT , $prompt . ' [' . $hint . ']: ' );
		$line = trim( (string) fgets( STDIN ) );
		if ( $line === '' ) return $default;
		return in_array( strtolower( $line ) , [ 'y' , 'yes' , 'д' , 'да' ] , true );
	}

	public static function help ( array $commands ) : void {
		self::out( 'SimplCMS corescripts' );
		self::out( '' );
		self::out( 'Usage: php corescripts/cli.php <command> [args]' );
		self::out( '' );
		self::out( 'Commands:' );
		foreach ( $commands as $c ) {
			if ( $c === 'help' ) continue;
			self::out( '  ' . $c );
		}
		self::out( '  help' );
	}
}