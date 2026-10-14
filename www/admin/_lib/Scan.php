<?PHP

# ============================================================
#   Scan — сканеры доступных значений для форм админки
#   Пути — через Paths (PROJECT_ROOT и logic определяются там)
# ============================================================

class Scan {

    # www/media/<real>/skin/* — только папки
    public static function skinsFor ( string $domain ) : array {
        return self::listDirs( Paths::skins( $domain ) );
    }

    # www/logic/* — только папки, где есть include.php
    public static function logics ( ) : array {
        $dir = Paths::project() . '/www/logic';
        $out = [ ];
        foreach ( self::listDirs( $dir ) as $name ) {
            if ( is_file( $dir . '/' . $name . '/include.php' ) ) {
                $out[ ] = $name;
            }
        }
        return $out;
    }

    # допустимые значения connect_type — совпадают с SettingsSet::validate()
    public static function connectTypes ( ) : array {
        return [ 'http://' , 'https://' ];
    }

    # --- дерево страниц аллиаса ---
    public static function pageTree ( string $base ) : array {
        if ( ! is_dir( $base ) ) return [ ];

        $root_file = $base . '/algoritm.php';
        if ( ! is_file( $root_file ) ) return [ ];

        $root = [
            'name'      => 'algoritm' ,
            'path'      => '' ,
            'class'     => 'algoritm' ,
            'has_file'  => true ,
            'meta'      => self::parseMeta( $root_file ) ,
            'children'  => [ ] ,
        ];

        foreach ( scandir( $base ) as $name ) {
            if ( $name === '.' || $name === '..' ) continue;
            if ( $name === 'algoritm_auth.php' )       continue;
            if ( $name === 'algoritm_error.php' )      continue;
            if ( $name === 'algoritm_pageshablon.php' ) continue;

            $dir = $base . '/' . $name;
            if ( ! is_dir( $dir ) ) continue;

            $root[ 'children' ][ ] = self::pageNode( $name , $dir , $name );
        }

        usort( $root[ 'children' ] , fn( $a , $b ) => strcmp( $a[ 'name' ] , $b[ 'name' ] ) );

        return [ $root ];
    }

    private static function pageNode ( string $name , string $dir , string $path ) : array {
        $cls  = 'algoritm' . ( $path === '' ? '' : '_' . str_replace( '/' , '_' , $path ) );
        $file = $dir . '/' . $cls . '.php';
        $has  = is_file( $file );

        $children = [ ];
        foreach ( scandir( $dir ) as $sub ) {
            if ( $sub === '.' || $sub === '..' ) continue;
            if ( $sub === 'algoritm_auth.php' )       continue;
            if ( $sub === 'algoritm_error.php' )      continue;
            if ( $sub === 'algoritm_pageshablon.php' ) continue;

            $sub_dir = $dir . '/' . $sub;
            if ( ! is_dir( $sub_dir ) ) continue;

            $children[ ] = self::pageNode( $sub , $sub_dir , $path . '/' . $sub );
        }
        usort( $children , fn( $a , $b ) => strcmp( $a[ 'name' ] , $b[ 'name' ] ) );

        return [
            'name'      => $name ,
            'path'      => $path ,
            'class'     => $cls ,
            'has_file'  => $has ,
            'meta'      => $has ? self::parseMeta( $file ) : [
                'available' => [ ] , 'auth' => false , 'search' => true , 'default' => false ,
            ] ,
            'children'  => $children ,
        ];
    }

    # парсит метаданные страницы из .data.json
    private static function parseMeta ( string $file ) : array {
        $default = [
            'available' => [ ] ,
            'auth'      => false ,
            'search'    => true ,
            'default'   => false ,
        ];

        $json = preg_replace( '/\.php$/' , '.data.json' , $file );
        $data = Json::read( $json );
        if ( ! is_array( $data ) || ! isset( $data[ 'default' ] ) ) return $default;

        $d = $data[ 'default' ];
        return [
            'available' => is_array( $d[ 'available' ] ?? null ) ? $d[ 'available' ] : [ ] ,
            'auth'      => ! empty( $d[ 'auth' ] ) ,
            'search'    => ! isset( $d[ 'search' ] ) || ! empty( $d[ 'search' ] ) ,
            'default'   => ! empty( $d[ 'default' ] ) ,
        ];
    }

    # блоки домена: папки alliasglobal/* кроме служебных
    public static function blocksFor ( string $domain ) : array {
        $dir = Paths::blocks( $domain );
        if ( ! is_dir( $dir ) ) return [ ];

        $out = [ ];
        foreach ( scandir( $dir ) as $name ) {
            if ( $name === '.' || $name === '..' ) continue;
            if ( in_array( $name , Blocks::SKIP , true ) ) continue;
            if ( ! is_dir( $dir . '/' . $name ) ) continue;
            $out[ ] = $name;
        }
        sort( $out );
        return $out;
    }

    # --- helper ---
    private static function listDirs ( string $dir ) : array {
        if ( ! is_dir( $dir ) ) return [ ];
        $out = [ ];
        foreach ( scandir( $dir ) as $name ) {
            if ( $name === '.' || $name === '..' ) continue;
            if ( ! is_dir( $dir . '/' . $name ) )  continue;
            $out[ ] = $name;
        }
        sort( $out );
        return $out;
    }
}