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

                # --- 1. переместить папку аллиаса в карантин ---
        $trash_path = $allias_path . '_trash_' . time();
        $in_trash = false;
        
        if ( is_dir( $allias_path ) ) {
            if ( rename( $allias_path , $trash_path ) ) {
                $in_trash = true;
                $r->step( 'Папка аллиаса в карантине' , true , Fs::rel( $trash_path ) );
                $ctx?->step( 'Папка аллиаса в карантине' , true , Fs::rel( $trash_path ) );
            } else {
                $r->error( "Не удалось переместить папку аллиаса в карантин" );
                return $r;
            }
        }

        # --- 2. site.config (под lock) ---
        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $domain , $name ) {
                $real = Paths::real( $domain );
                $cfg->removeKeys( [ 'alliases_real'      , $real , $name ] );
                $cfg->removeKeys( [ 'alliases_available' , $real , $name ] );
                $cfg->removeKeys( [ 'fillers'            , $real , $name ] );
            } );
            $r->step( 'site.config обновлён' , true , $name );
            $ctx?->step( 'site.config обновлён' , true , $name );
            
            # --- 3. окончательное удаление папки и filler'ов ---
            if ( $in_trash && is_dir( $trash_path ) ) {
                Fs::rmrf( $trash_path );
            }
            
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
            
        } catch ( Throwable $e ) {
            # --- ОТКАТ ---
            if ( $in_trash && is_dir( $trash_path ) ) {
                rename( $trash_path , $allias_path );
                $r->error( "Ошибка конфига: " . $e->getMessage() . " (папка восстановлена)" );
            } else {
                $r->error( "site.config: " . $e->getMessage( ) );
            }
        }

        return $r;
    }
}