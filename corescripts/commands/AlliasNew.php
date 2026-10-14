<?PHP

class AlliasNew {

    # --- план ---
    public static function plan ( array $params ) : array {
        $name   = $params[ 'name' ] ?? '';
        $domain = $params[ 'domain' ] ?? 'damoony.ru';

        return [
            'name'   => $name ,
            'domain' => $domain ,
            'path'   => 'www/logic/' . Paths::logic() . '/domain/' . $domain . '/allias/' . $name ,
        ];
    }

    # --- выполнение ---
    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        # 1. валидация имени
        $name = (string) ( $params[ 'name' ] ?? '' );
        if ( $err = Validate::allias_name( $name ) ) {
            $r->step( 'Валидация имени' , false , $err );
            return $r;
        }
        $r->step( 'Валидация имени' , true , $name );
        $ctx?->step( 'Валидация имени' , true , $name );

        # 2. домен
        $domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }
        $r->step( 'Валидация домена' , true , $domain );
        $ctx?->step( 'Валидация домена' , true , $domain );

        $domain_path = Paths::domain( $domain );
        if ( ! is_dir( $domain_path ) ) {
            $r->error( "Домен не найден: {$domain_path}" );
            return $r;
        }

        # 3. путь аллиаса
        $allias_path = Paths::allias( $domain , $name );
        if ( Fs::exists( $allias_path ) ) {
            $r->error( "Аллиас уже существует: {$allias_path}" );
            return $r;
        }

        # 4. namespace домена
        $ns = Paths::ns( $domain );
        if ( $ns === null ) {
            $r->error( "Не найден domains_namespace для {$domain} в site.config" );
            return $r;
        }
        $r->step( 'Namespace домена' , true , $ns );
        $ctx?->step( 'Namespace домена' , true , $ns );

        # 5. копировать структуру аллиаса
        $src_allias = CS_TPL . '/allias';
        if ( ! is_dir( $src_allias ) ) {
            $r->error( "Шаблон аллиаса не найден: {$src_allias}" );
            return $r;
        }

        $vars = [
            'name'      => $name ,
            'Name'      => ucfirst( $name ) ,
            'domain'    => $domain ,
            'domain_ns' => $ns ,
        ];

        $files = Fs::scan_recursive( $src_allias );
        foreach ( $files as $rel ) {
            $content = file_get_contents( $src_allias . '/' . $rel );
            if ( $content === false ) continue;

            $content = Template::render_str( $content , $vars );

            $out_rel = preg_replace( '/\.tpl$/' , '' , $rel );
            $out = $allias_path . '/' . $out_rel;
            Fs::write( $out , $content );
            $r->file( $out );
        }

        # algoritm.data.json
        $tpl_json = CS_TPL . '/allias/algoritm/algoritm.data.json.tpl';
        if ( is_file( $tpl_json ) ) {
            $out = $allias_path . '/algoritm/algoritm.data.json';
            if ( ! Fs::exists( $out ) ) {
                Fs::write( $out , file_get_contents( $tpl_json ) );
                $r->file( $out );
            }
        }

        # algoritm_lang.data.json
        $tpl_lang_json = CS_TPL . '/allias/algoritm/lang/algoritm_lang.data.json.tpl';
        if ( is_file( $tpl_lang_json ) ) {
            $out = $allias_path . '/algoritm/lang/algoritm_lang.data.json';
            if ( ! Fs::exists( $out ) ) {
                Fs::write( $out , file_get_contents( $tpl_lang_json ) );
                $r->file( $out );
            }
        }

        $r->step( 'Структура аллиаса' , true , count( $files ) . ' файлов' );
        $ctx?->step( 'Структура аллиаса' , true , count( $files ) . ' файлов' );

        # 6. создать filler'ы для всех блоков
        $src_block      = CS_TPL . '/filler/block.php.tpl';
        $src_block_data = CS_TPL . '/filler/block.data.php.tpl';

        if ( ! is_file( $src_block ) || ! is_file( $src_block_data ) ) {
            $r->warn( 'Шаблоны filler не найдены — пропускаю' );
        } else {
            foreach ( Blocks::LIST as $block ) {
                $block_dir = Paths::block( $domain , $block );
                if ( ! is_dir( $block_dir ) ) {
                    $r->warn( "Блок не найден: {$block}" );
                    continue;
                }

                $vars_block = $vars + [ 'block' => $block ];

                $c = file_get_contents( $src_block );
                $c = Template::render_str( $c , $vars_block );
                $out = $block_dir . '/' . $block . '_' . $name . '.php';
                Fs::write( $out , $c );
                $r->file( $out );

                $c = file_get_contents( $src_block_data );
                $c = Template::render_str( $c , $vars_block );
                $out = $block_dir . '/' . $block . '_' . $name . '.data.php';
                Fs::write( $out , $c );
                $r->file( $out );
            }
            $r->step( "Filler'ы" , true , count( Blocks::LIST ) . ' блоков' );
            $ctx?->step( "Filler'ы" , true , count( Blocks::LIST ) . ' блоков' );
        }

        # 7. lint
        $lint_errors = 0;
        foreach ( $r->created as $full ) {
            if ( substr( $full , -4 ) !== '.php' ) continue;
            if ( $err = Template::lint( $full ) ) {
                $r->warn( "Lint: " . Fs::rel( $full ) . ": {$err}" );
                $lint_errors++;
            }
        }
        if ( $lint_errors === 0 ) {
            $r->step( 'Синтаксис PHP' , true , 'все файлы OK' );
            $ctx?->step( 'Синтаксис PHP' , true , 'все файлы OK' );
        }

        # 8. регистрация
        if ( ! empty( $params[ 'register' ] ) ) {
            self::register( $r , $domain , $name , $ctx );
        }

        return $r;
    }

    # --- регистрация ---
    private static function register ( Result $r , string $domain , string $name , ?Context $ctx = null ) : void {
        try {
            Config::mutate( Paths::config() , function ( Config $cfg ) use ( $domain , $name ) {
                if ( ! $cfg->hasKeys( [ 'alliases_real' , $domain , $name ] ) ) {
                    $cfg->setKeys( [ 'alliases_real' , $domain , $name ] , $name );
                }

                if ( ! $cfg->hasKeys( [ 'alliases_available' , $domain , $name ] ) ) {
                    $cfg->setKeys( [ 'alliases_available' , $domain , $name ] , true );
                }

                if ( ! $cfg->hasKeys( [ 'fillers' , $domain , $name ] ) ) {
                    $f = [ ];
                    foreach ( Blocks::LIST as $b ) $f[ $b ] = false;
                    $cfg->setKeys( [ 'fillers' , $domain , $name ] , $f );
                }
            } );

            $r->step( 'site.config: alliases_real'      , true , $name );
            $r->step( 'site.config: alliases_available' , true , $name );
            $r->step( 'site.config: fillers'            , true , $name );
            $r->step( 'site.config сохранён'            , true , 'backup: site.config.bak' );
            $ctx?->step( 'site.config сохранён' , true , 'backup: site.config.bak' );
        } catch ( Throwable $e ) {
            $r->error( "Не сохранить site.config: " . $e->getMessage( ) );
        }
    }
}