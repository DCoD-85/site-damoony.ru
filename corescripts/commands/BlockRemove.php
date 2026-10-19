<?PHP
class BlockRemove {
    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );
        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $name   = (string) ( $params[ 'name' ]   ?? '' );
        if ( $err = Validate::domain( $domain ) ) { $r->error( $err ); return $r; }
        if ( $err = Validate::allias_name( $name ) ) { $r->error( $err ); return $r; }
        $block_path = Paths::block( $domain , $name );
        if ( ! is_dir( $block_path ) ) { $r->error( "Блок не найден: {$name}" ); return $r; }
        Fs::rmrf( $block_path );
        $r->step( 'Папка блока удалена' , true , $name );
        $r->warn( "Не забудь удалить блок из alliasglobal.php вручную!" );
        return $r;
    }
}
