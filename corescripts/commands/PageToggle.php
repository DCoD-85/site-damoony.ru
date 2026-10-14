<?PHP

class PageToggle {

    public static function plan ( array $params ) : array {
        return [
            'domain' => $params[ 'domain' ] ?? '' ,
            'allias' => $params[ 'allias' ] ?? '' ,
            'path'   => $params[ 'path' ]   ?? '' ,
            'key'    => $params[ 'key' ]    ?? '' ,
            'value'  => $params[ 'value' ]  ?? '' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $domain = (string) ( $params[ 'domain' ] ?? '' );
        $allias = (string) ( $params[ 'allias' ] ?? '' );
        $path   = trim( (string) ( $params[ 'path' ] ?? '' ) , '/' );
        $key    = (string) ( $params[ 'key' ]    ?? '' );
        $value  = (string) ( $params[ 'value' ]  ?? '' );

        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }
        if ( $err = Validate::allias_name( $allias ) ) {
            $r->step( 'Валидация аллиаса' , false , $err );
            return $r;
        }
        if ( $err = Validate::page_path( $path ) ) {
            $r->step( 'Валидация пути' , false , $err );
            return $r;
        }
        if ( ! in_array( $key , [ 'auth' , 'search' , 'default' ] , true ) ) {
            $r->step( 'Валидация ключа' , false , "недопустимый ключ: {$key}" );
            return $r;
        }

        $base      = Paths::algoritm( $domain , $allias );
        $cls_short = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) );
        $sub_dir   = $path === '' ? $base : $base . '/' . $path;
        $php_file  = $sub_dir . '/' . $cls_short . '.php';
        $json_file = $sub_dir . '/' . $cls_short . '.data.json';

        if ( ! is_file( $php_file ) ) {
            $r->error( "Страница не найдена: " . Fs::rel( $php_file ) );
            return $r;
        }

        $data = [ 'default' => [
            'available' => [ '' ] ,
            'auth'      => false ,
            'search'    => true ,
            'default'   => false ,
        ] ];

        $cur = Json::read( $json_file );
        if ( is_array( $cur ) && isset( $cur[ 'default' ] ) && is_array( $cur[ 'default' ] ) ) {
            $data[ 'default' ] = array_merge( $data[ 'default' ] , $cur[ 'default' ] );
        }

        $data[ 'default' ][ $key ] = ( $value === '0' ) ? false : true;

        try {
            Json::writeAtomic( $json_file , $data );
            $r->step( "{$key} = " . ( $data[ 'default' ][ $key ] ? 'true' : 'false' ) , true , Fs::rel( $json_file ) );
        } catch ( Throwable $e ) {
            $r->error( 'Не сохранить: ' . $e->getMessage( ) );
        }

        return $r;
    }
}