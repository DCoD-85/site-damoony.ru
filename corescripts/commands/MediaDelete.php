<?PHP

class MediaDelete {
    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result();
        $domain = (string)( $params['domain'] ?? '' );
        $file   = basename( (string)( $params['file'] ?? '' ) ); // basename() защищает от Path Traversal атак

        if ( empty($domain) || empty($file) ) {
            $r->error('Неверные параметры');
            return $r;
        }

        $path = Paths::media( $domain ) . '/' . $file;
        
        if ( is_file( $path ) ) {
            unlink( $path );
            $r->step( 'Файл удален' , true , $file );
        } else {
            $r->error('Файл не найден');
        }
        
        return $r;
    }
}