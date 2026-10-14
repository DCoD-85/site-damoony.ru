<?PHP

class AlliasRemove {

    public static function plan ( array $params ) : array {
        return [
            'name'   => $params[ 'name' ] ?? '' ,
            'domain' => $params[ 'domain' ] ?? 'damoony.ru' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $name   = (string) ( $params[ 'name' ] ?? '' );
        $domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
        $force  = ! empty( $params[ 'force' ] );

        if ( $err = Validate::allias_name( $name ) ) {
            $r->step( 'Валидация имени' , false , $err );
            return $r;
        }
        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }

        $domain_path = Paths::domain( $domain );
        $allias_path = Paths::allias( $domain , $name );

        if ( ! is_dir( $domain_path ) ) {
            $r->error( "Домен не найден: {$domain_path}" );
            return $r;
        }
        if ( ! is_dir( $allias_path ) ) {
            $r->warn( "Папка аллиаса не найдена: {$allias_path} — только из config" );
        }

        # --- подтверждение ---
        if ( ! $force ) {
            Cli::out( '' );
            Cli::warn( "Будет удалено: {$name} ({$domain})" );
            Cli::out( '  - ' . Fs::rel( $allias_path ) );
            Cli::out( '  - filler-файлы во всех блоках' );
            Cli::out( '  - запись в site.config' );
            Cli::out( '' );
            if ( ! Cli::confirm( 'Удалить?' , false ) ) {
                $r->step( 'Отменено пользователем' , true , '' );
                return $r;
            }
        }

        # --- 1. удалить папку аллиаса ---
        if ( is_dir( $allias_path ) ) {
            Fs::rmrf( $allias_path );
            $r->step( 'Удалена папка аллиаса' , true , Fs::rel( $allias_path ) );
            $ctx?->step( 'Удалена папка аллиаса' , true , Fs::rel( $allias_path ) );
        }

        # --- 2. удалить filler'ы ---
        $removed = 0;
        foreach ( Blocks::LIST as $block ) {
            $files = [
                Paths::block( $domain , $block ) . '/' . $block . '_' . $name . '.php' ,
                Paths::block( $domain , $block ) . '/' . $block . '_' . $name . '.data.php' ,
            ];
            foreach ( $files as $f ) {
                if ( is_file( $f ) ) {
                    @unlink( $f );
                    $removed++;
                }
            }
        }
        $r->step( "Удалено filler-файлов" , true , (string) $removed );
        $ctx?->step( "Удалено filler-файлов" , true , (string) $removed );

        # --- 3. site.config (под lock) ---
        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $domain , $name ) {
                $cfg->removeKeys( [ 'alliases_real'      , $domain , $name ] );
                $cfg->removeKeys( [ 'alliases_available' , $domain , $name ] );
                $cfg->removeKeys( [ 'fillers'            , $domain , $name ] );
            } );
            $r->step( 'site.config обновлён' , true , $name );
            $ctx?->step( 'site.config обновлён' , true , $name );
        } catch ( Throwable $e ) {
            $r->error( "site.config: " . $e->getMessage( ) );
        }

        return $r;
    }
}