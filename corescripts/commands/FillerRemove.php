<?PHP

class FillerRemove {

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $allias = (string) ( $params[ 'allias' ] ?? '' );
        $block  = (string) ( $params[ 'block' ]  ?? '' );

        if ( $err = Validate::domain( $domain ) )     { $r->error( $err ); return $r; }
        if ( $err = Validate::allias_name( $allias ) ){ $r->error( $err ); return $r; }
        if ( ! Blocks::isBlock( $block ) )            { $r->error( "Неверный блок: {$block}" ); return $r; }

        $real      = Paths::real( $domain );
        $block_dir = Paths::block( $domain , $block );
        $php_file  = $block_dir . '/' . $block . '_' . $allias . '.php';
        $data_file = $block_dir . '/' . $block . '_' . $allias . '.data.php';

        $has_file = false;
        if ( is_file( $php_file ) )  { @unlink( $php_file );  $has_file = true; }
        if ( is_file( $data_file ) ) { @unlink( $data_file ); $has_file = true; }

        if ( $has_file ) {
            $r->step( 'Файлы удалены' , true , $block . '_' . $allias . '.php' );
        } else {
            $r->warn( 'Файлы филлера не найдены' );
        }

        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $allias , $block ) {
                $cfg->removeKeys( [ 'fillers' , $real , $allias , $block ] );
            } );
            $r->step( 'Конфиг обновлён' , true , $real );
        } catch ( Throwable $e ) {
            $r->error( 'Ошибка конфига: ' . $e->getMessage( ) );
        }

        return $r;
    }
}
