<?PHP
class BlockRemove {
    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );
        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $name   = (string) ( $params[ 'name' ]   ?? '' );

        if ( $err = Validate::domain( $domain ) ) { $r->error( $err ); return $r; }
        if ( $err = Validate::allias_name( $name ) ) { $r->error( $err ); return $r; }

        $real = Paths::real( $domain );
        $block_path = Paths::block( $domain , $name );
        if ( ! is_dir( $block_path ) ) { $r->error( "Блок не найден: {$name}" ); return $r; }

        # 1. Удаляем папку блока
        Fs::rmrf( $block_path );
        $r->step( 'Папка блока удалена' , true , $name );

        # 2. Чистим site.config (fillers)
        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $real , $name ) {
                $fillers = $cfg->getKeys( [ 'fillers' , $real ] , [] );
                foreach ( $fillers as $allias => $blocks ) {
                    $cfg->removeKeys( [ 'fillers' , $real , $allias , $name ] );
                }
            } );
            $r->step( 'Конфиг fillers очищен' , true , $name );
        } catch ( Throwable $e ) {
            $r->warn( 'Не удалось почистить fillers: ' . $e->getMessage() );
        }

        # 3. Чистим alliasglobal.php
        $ag_file = Paths::domain( $domain ) . '/alliasglobal.php';
        if ( is_file( $ag_file ) ) {
            $c = file_get_contents( $ag_file );
            
            // Удаляем property: public alliasglobal_adv1 $adv1;
            $c = preg_replace( "/public\s+alliasglobal_" . $name . "\s+\\\$" . $name . "\s*;\s*\n?/", "", $c );
            
            // Удаляем include_once
            $c = preg_replace( "/\(\s*include_once\s+\\\$path\s*=\s*__DIR__\s*\.\s*DS\s*\.\s*'alliasglobal'\s*\.\s*DS\s*\.\s*'" . $name . "'\s*\.\s*DS\s*\.\s*'" . $name . "\.php'\s*\)\s*or\s*error_file\(.*?\);\s*\n?/", "", $c );
            
            // Удаляем init: $this->adv1 = new alliasglobal_adv1( $this );
            $c = preg_replace( "/\\\$this->" . $name . "\s*=\s*new\s+alliasglobal_" . $name . "\(\s*\\\$this\s*\);\s*\n?/", "", $c );

            file_put_contents( $ag_file, $c );
            $r->step( 'alliasglobal.php очищен' , true , $name );
        }

        return $r;
    }
}
