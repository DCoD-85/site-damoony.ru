<?PHP

# ============================================================
#   Blocks — единый список блоков alliasglobal.
#   Заменяет хардкоды в:
#     AlliasNew::BLOCKS
#     AlliasRemove (inline)
#     DomainNew::register (inline)
#     FillerNew (логика валидации)
#     admin/Scan::blocksFor (skip-список)
# ============================================================

class Blocks {

    # --- основной список (7 блоков) ---
    public const LIST = [
        'top' ,
        'left' ,
        'center' ,
        'right' ,
        'bottom' ,
        'policy' ,
        'debug' ,
    ];

    # --- служебные папки, которые не являются блоками ---
    public const SKIP = [
        'script' ,
    ];

    # --- все ли имена валидны ---
    public static function isBlock ( string $name ) : bool {
        return in_array( $name , self::LIST , true );
    }

    # --- список блоков без debug (для filler:new) ---
    public static function forFiller () : array {
        return array_values( array_diff( self::LIST , self::SKIP ) );
    }
}