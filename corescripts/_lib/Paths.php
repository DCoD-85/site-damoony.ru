<?PHP

# ============================================================
#   Paths — единая точка доступа к путям проекта.
#   Заменяет хардкоды 'alpha.0.0' и ручные конкатенации.
#
#   Кэширует logic в статике — читает site.config один раз
#   за процесс.
# ============================================================

class Paths {

    private static ?string $logic_cache = null;

    # --- корень ---
    public static function project () : string {
        return PROJECT_ROOT;
    }

    # --- конфиг ---
    public static function config () : string {
        return self::project() . '/config/site.config';
    }

    # --- logic (имя из site.config, fallback alpha.0.0) ---
    public static function logic () : string {
        if ( self::$logic_cache !== null ) return self::$logic_cache;

        $logic = 'alpha.0.0';
        $path  = self::config();

        if ( is_file( $path ) ) {
            $raw = @file_get_contents( $path );
            if ( $raw !== false ) {
                $data = @json_decode( $raw , true );
                if ( is_array( $data ) && ! empty( $data[ 'logic' ] ) && is_string( $data[ 'logic' ] ) ) {
                    $logic = $data[ 'logic' ];
                }
            }
        }

        return self::$logic_cache = $logic;
    }

    public static function reset ( ) : void {
        self::$logic_cache = null;
    }

    # --- www/logic/<logic> ---
    public static function logicPath () : string {
        return self::project() . '/www/logic/' . self::logic();
    }

    # --- domain ---
    public static function domain ( string $domain ) : string {
        return self::logicPath() . '/domain/' . $domain;
    }

    # --- allias ---
    public static function allias ( string $domain , string $allias ) : string {
        return self::domain( $domain ) . '/allias/' . $allias;
    }

    # --- algoritm (внутри аллиаса) ---
    public static function algoritm ( string $domain , string $allias ) : string {
        return self::allias( $domain , $allias ) . '/algoritm';
    }

    # --- alliasglobal (блоки домена) ---
    public static function blocks ( string $domain ) : string {
        return self::domain( $domain ) . '/alliasglobal';
    }

    public static function block ( string $domain , string $block ) : string {
        return self::blocks( $domain ) . '/' . $block;
    }

    # --- media ---
    public static function media ( string $domain ) : string {
        return self::project() . '/www/media/' . $domain;
    }

    public static function skins ( string $domain ) : string {
        return self::media( $domain ) . '/skin';
    }

    # --- namespace домена из site.config ---
    public static function ns ( string $domain ) : ?string {
        $path = self::config();
        if ( ! is_file( $path ) ) return null;

        $raw = @file_get_contents( $path );
        if ( $raw === false ) return null;

        $data = @json_decode( $raw , true );
        if ( ! is_array( $data ) ) return null;

        $ns = $data[ 'domains_namespace' ][ $domain ] ?? null;
        return is_string( $ns ) && $ns !== '' ? $ns : null;
    }
}