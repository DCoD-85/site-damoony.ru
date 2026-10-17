<?PHP

# ============================================================
#   Paths — единая точка доступа к путям проекта.
#   Заменяет хардкоды 'alpha.0.0' и ручные конкатенации.
#
#   ВАЖНО: namespace и папки — по real-домену.
#   site.config:
#       "domains_real": {
#           "damoony2.ru":  "damoony.ru",   ← key=внешний, value=real
#           "damoony.online":"damoony.ru",
#           "damoony.ru":   "damoony.ru"
#       },
#       "domains_namespace": {
#           "damoony.ru":   "damoony_ru"    ← key=real
#       }
#
#   Paths::real('damoony2.ru')  → 'damoony.ru'
#   Paths::ns('damoony2.ru')    → 'damoony_ru'
#   Paths::domain('damoony2.ru') → '.../domain/damoony.ru'
# ============================================================

class Paths {

    private static ?array $cfg = null;

    # --- конфиг: загружаем один раз за процесс ---
    private static function cfg ( ) : array {
        if ( self::$cfg !== null ) return self::$cfg;

        $path = self::project() . '/config/site.config';
        if ( ! is_file( $path ) ) return self::$cfg = [ ];

        $raw = @file_get_contents( $path );
        if ( $raw === false ) return self::$cfg = [ ];

        $data = @json_decode( $raw , true );
        return self::$cfg = is_array( $data ) ? $data : [ ];
    }

    public static function reset ( ) : void {
        self::$cfg = null;
    }

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
        $logic = self::cfg()[ 'logic' ] ?? null;
        return is_string( $logic ) && $logic !== '' ? $logic : 'alpha.0.0';
    }

    # --- real domain: damoony2.ru → damoony.ru ---
    #     если домена нет в конфиге (новый) — возвращаем как есть
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
        return self::project() . '/logic/' . self::logic();
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