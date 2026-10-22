<?PHP

# ============================================================
#   Paths — единая точка доступа к путям проекта.
#   site.config   — структура (domains_*, alliases_*, fillers)
#   engine.config — настройки движка (logic, logic_type и т.д.)
# ============================================================

class Paths {

    private static ?array $site_cfg   = null;
    private static ?array $engine_cfg = null;

    # --- site.config: только структура (domains_real, domains_namespace) ---
    private static function cfg ( ) : array {
        if ( self::$site_cfg !== null ) return self::$site_cfg;

        $path = self::project() . '/config/site.config';
        if ( ! is_file( $path ) ) return self::$site_cfg = [ ];

        $raw = @file_get_contents( $path );
        if ( $raw === false ) return self::$site_cfg = [ ];

        $data = @json_decode( $raw , true );
        return self::$site_cfg = is_array( $data ) ? $data : [ ];
    }

    # --- engine.config: настройки движка (fallback на cms.config) ---
    private static function engineCfg ( ) : array {
        if ( self::$engine_cfg !== null ) return self::$engine_cfg;

        $path = self::project() . '/config/engine.config';
        if ( ! is_file( $path ) ) $path = self::project() . '/config/cms.config'; // переходный период
        if ( ! is_file( $path ) ) return self::$engine_cfg = [ ];

        $raw = @file_get_contents( $path );
        if ( $raw === false ) return self::$engine_cfg = [ ];

        $data = @json_decode( $raw , true );
        return self::$engine_cfg = is_array( $data ) ? $data : [ ];
    }

    public static function reset ( ) : void {
        self::$site_cfg   = null;
        self::$engine_cfg = null;
    }

    # --- корень ---
    public static function project () : string {
        return PROJECT_ROOT;
    }

    # --- пути к конфигам ---
    public static function config () : string {
        return self::project() . '/config/site.config';
    }

    public static function engine () : string {
        return self::project() . '/config/engine.config';
    }

    # --- logic (из engine.config, fallback alpha.0.0) ---
    public static function logic () : string {
        $logic = self::engineCfg()[ 'logic' ] ?? null;
        return is_string( $logic ) && $logic !== '' ? $logic : 'alpha.0.0';
    }

    # --- real domain: damoony2.ru → damoony.ru ---
    public static function real ( string $domain ) : string {
        $real = self::cfg()[ 'domains_real' ][ $domain ] ?? null;
        return is_string( $real ) && $real !== '' ? $real : $domain;
    }

    # --- namespace домена (по real) ---
    public static function ns ( string $domain ) : ?string {
        $real = self::real( $domain );
        $ns   = self::cfg()[ 'domains_namespace' ][ $real ] ?? null;
        return is_string( $ns ) && $ns !== '' ? $ns : null;
    }

    # --- www/logic/<logic> ---
    public static function logicPath () : string {
        return self::project() . '/app/' . self::logic();
    }

    # --- domain (по real) ---
    public static function domain ( string $domain ) : string {
        return self::logicPath() . '/domain/' . self::real( $domain );
    }

    # --- allias (по real) ---
    public static function allias ( string $domain , string $allias ) : string {
        return self::domain( $domain ) . '/allias/' . $allias;
    }

    # --- algoritm (внутри аллиаса) ---
    public static function algoritm ( string $domain , string $allias ) : string {
        return self::allias( $domain , $allias ) . '/algoritm';
    }

    # --- alliasglobal (блоки домена, по real) ---
    public static function blocks ( string $domain ) : string {
        return self::domain( $domain ) . '/alliasglobal';
    }

    public static function block ( string $domain , string $block ) : string {
        return self::blocks( $domain ) . '/' . $block;
    }

    # --- media (по real) ---
    public static function media ( string $domain ) : string {
        return self::project() . '/www/media/' . self::real( $domain );
    }

    public static function skins ( string $domain ) : string {
        return self::media( $domain ) . '/skin';
    }
}