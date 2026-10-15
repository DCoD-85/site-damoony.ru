<?PHP

# ============================================================
#   Assert — статические проверки для тестов.
#   При провале бросает AssertException с готовым сообщением.
# ============================================================

class Assert {

    public static function true ( mixed $v , string $msg = '' ) : void {
        if ( $v !== true ) self::fail( $msg , 'true' , var_export( $v , true ) );
    }

    public static function false ( mixed $v , string $msg = '' ) : void {
        if ( $v !== false ) self::fail( $msg , 'false' , var_export( $v , true ) );
    }

    public static function null ( mixed $v , string $msg = '' ) : void {
        if ( $v !== null ) self::fail( $msg , 'null' , gettype( $v ) . ' ' . var_export( $v , true ) );
    }

    public static function notNull ( mixed $v , string $msg = '' ) : void {
        if ( $v === null ) self::fail( $msg , 'not null' , 'null' );
    }

    public static function eq ( mixed $expected , mixed $actual , string $msg = '' ) : void {
        if ( $expected != $actual ) self::fail( $msg , self::fmt( $expected ) , self::fmt( $actual ) );
    }

    public static function same ( mixed $expected , mixed $actual , string $msg = '' ) : void {
        if ( $expected !== $actual ) self::fail( $msg , self::fmt( $expected ) , self::fmt( $actual ) );
    }

    public static function contains ( string $needle , string $haystack , string $msg = '' ) : void {
        if ( ! str_contains( $haystack , $needle ) ) {
            self::fail( $msg , "contains: {$needle}" , substr( $haystack , 0 , 400 ) );
        }
    }

    public static function notContains ( string $needle , string $haystack , string $msg = '' ) : void {
        if ( str_contains( $haystack , $needle ) ) {
            self::fail( $msg , "not contains: {$needle}" , 'found' );
        }
    }

    public static function instance ( string $class , mixed $obj , string $msg = '' ) : void {
        if ( ! ( $obj instanceof $class ) ) {
            $actual = is_object( $obj ) ? get_class( $obj ) : gettype( $obj );
            self::fail( $msg , "instanceof {$class}" , $actual );
        }
    }

    public static function count ( int $expected , array $arr , string $msg = '' ) : void {
        $actual = count( $arr );
        if ( $expected !== $actual ) {
            self::fail( $msg , "count={$expected}" , "count={$actual}" );
        }
    }

    # --- internals ---

    private static function fail ( string $msg , string $expected , string $actual ) : void {
        $out = $msg !== '' ? $msg . "\n" : '';
        $out .= "  expected: {$expected}\n";
        $out .= "  actual:   {$actual}";
        throw new AssertException( $out );
    }

    private static function fmt ( mixed $v ) : string {
        if ( is_string( $v ) && strlen( $v ) > 200 ) {
            return substr( $v , 0 , 200 ) . '... (len=' . strlen( $v ) . ')';
        }
        return var_export( $v , true );
    }
}