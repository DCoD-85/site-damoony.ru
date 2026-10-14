<?PHP

class VarsExtract {

    public static function plan ( array $params ) : array {
        return [
            'domain' => $params[ 'domain' ] ?? '' ,
            'allias' => $params[ 'allias' ] ?? '' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $allias = (string) ( $params[ 'allias' ] ?? '' );
        $clean  = ! isset( $params[ 'no-clean' ] );
        $dry    = ! empty( $params[ 'dry' ] );

        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }
        if ( $err = Validate::allias_name( $allias ) ) {
            $r->step( 'Валидация аллиаса' , false , $err );
            return $r;
        }

        $base = Paths::algoritm( $domain , $allias );
        if ( ! is_dir( $base ) ) {
            $r->error( "Каталог не найден: {$base}" );
            return $r;
        }

        $files   = Fs::scan_recursive( $base );
        $created = 0;
        $skipped = 0;
        $cleaned = 0;

        foreach ( $files as $rel ) {
            $full = $base . '/' . $rel;

            if ( substr( $rel , -4 ) !== '.php' ) continue;
            if ( strpos( $rel , '.data.' ) !== false ) continue;
            if ( basename( $rel ) === 'algoritm_pageshablon.php' ) continue;

            $json_rel  = preg_replace( '/\.php$/' , '.data.json' , $rel );
            $json_full = $base . '/' . $json_rel;

            $content = file_get_contents( $full );
            if ( $content === false ) continue;

            # --- 1. .data.json ---
            if ( ! Fs::exists( $json_full ) ) {
                $available = self::extract_available( $content );
                $auth      = self::extract_bool( $content , 'auth' );
                $search    = self::extract_bool( $content , 'search' , true );
                $default   = self::extract_bool( $content , 'default' );

                $data = [
                    'default' => [
                        'available' => $available ,
                        'auth'      => $auth ,
                        'search'    => $search ,
                        'default'   => $default ,
                    ] ,
                ];

                if ( ! $dry ) Json::writeAtomic( $json_full , $data );

                $r->file( $json_full );
                $created++;
            } else {
                $skipped++;
            }

            # --- 2. очистка .php ---
            if ( $clean && ! $dry ) {
                $cleaned_content = self::clean_php( $content );

                if ( $cleaned_content !== $content ) {
                    Fs::writeAtomic( $full , $cleaned_content );
                    $r->file( $full );
                    $cleaned++;
                }
            }
        }

        $r->step( 'Создано .data.json' , true , (string) $created );
        $r->step( 'Пропущено (json уже есть)' , true , (string) $skipped );
        $r->step( 'Очищено .php' , true , (string) $cleaned );

        return $r;
    }

    private static function extract_available ( string $content ) : array {
        if ( ! preg_match( '/public\s+array\s+\$available\s*=\s*\[([^\]]*)\]/s' , $content , $m ) ) {
            return [ '' ];
        }
        preg_match_all( "/'((?:[^'\\\\]|\\\\.)*)'/" , $m[ 1 ] , $mm );
        return array_map( fn( $s ) => str_replace( "\\'" , "'" , $s ) , $mm[ 1 ] );
    }

    private static function extract_bool ( string $content , string $name , bool $default = false ) : bool {
        if ( ! preg_match( '/public\s+bool\s+\$' . preg_quote( $name , '/' ) . '\s*=\s*(true|false)/' , $content , $m ) ) {
            return $default;
        }
        return $m[ 1 ] === 'true';
    }

    # Очистка .php — поддержка LF и CRLF
    private static function clean_php ( string $content ) : string {
        $patterns = [
            '/^\s*public\s+array\s+\$available\s*=\s*\[[^\]]*\]\s*;\s*\r?\n/m' ,
            '/^\s*public\s+bool\s+\$auth\s*=\s*(?:true|false)\s*;\s*\r?\n/m' ,
            '/^\s*public\s+bool\s+\$search\s*=\s*(?:true|false)\s*;\s*\r?\n/m' ,
            '/^\s*public\s+bool\s+\$default\s*=\s*(?:true|false)\s*;\s*\r?\n/m' ,
            '/^\s*public\s+bool\s+\$logged\s*=\s*(?:true|false)\s*;\s*\r?\n/m' ,
        ];

        foreach ( $patterns as $p ) {
            $content = preg_replace( $p , '' , $content );
        }

        # убираем возможные двойные пустые строки
        $content = preg_replace( "/\n{3,}/" , "\n\n" , $content );

        return $content;
    }
}