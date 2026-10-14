<?PHP

class Config {

    private string $path;
    private array  $data;

    public function __construct ( string $path ) {
        $this->path = $path;
        if ( ! is_file( $path ) ) {
            throw new RuntimeException( "Config not found: {$path}" );
        }
        $json = file_get_contents( $path );
        $data = json_decode( $json , true );
        if ( ! is_array( $data ) ) {
            throw new RuntimeException( "Invalid JSON: {$path}" );
        }
        $this->data = $data;
    }

    public function data ( ) : array {
        return $this->data;
    }

    public function get ( string $path , mixed $default = null ) : mixed {
        $keys = explode( '.' , $path );
        $ref  = $this->data;
        foreach ( $keys as $k ) {
            if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return $default;
            $ref = $ref[ $k ];
        }
        return $ref;
    }

    public function has ( string $path ) : bool {
        $keys = explode( '.' , $path );
        $ref  = $this->data;
        foreach ( $keys as $k ) {
            if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return false;
            $ref = $ref[ $k ];
        }
        return true;
    }

    # --- array-key API (без парсинга точек) ---

    public function hasKeys ( array $keys ) : bool {
        $ref = $this->data;
        foreach ( $keys as $k ) {
            if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return false;
            $ref = $ref[ $k ];
        }
        return true;
    }

    public function getKeys ( array $keys , mixed $default = null ) : mixed {
        $ref = $this->data;
        foreach ( $keys as $k ) {
            if ( ! is_array( $ref ) || ! array_key_exists( $k , $ref ) ) return $default;
            $ref = $ref[ $k ];
        }
        return $ref;
    }

    public function setKeys ( array $keys , mixed $value ) : void {
        $ref = &$this->data;
        foreach ( $keys as $k ) {
            if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) {
                $ref[ $k ] = [ ];
            }
            $ref = &$ref[ $k ];
        }
        $ref = $value;
    }

    public function set ( string $path , mixed $value ) : void {
        $keys = explode( '.' , $path );
        $ref  = &$this->data;
        foreach ( $keys as $k ) {
            if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) {
                $ref[ $k ] = [ ];
            }
            $ref = &$ref[ $k ];
        }
        $ref = $value;
    }

    public function remove ( string $path ) : void {
        $keys = explode( '.' , $path );
        $last = array_pop( $keys );

        $ref = &$this->data;
        foreach ( $keys as $k ) {
            if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) return;
            $ref = &$ref[ $k ];
        }
        unset( $ref[ $last ] );
    }

    public function removeKeys ( array $keys ) : void {
        if ( empty( $keys ) ) return;
        $last = array_pop( $keys );

        $ref = &$this->data;
        foreach ( $keys as $k ) {
            if ( ! isset( $ref[ $k ] ) || ! is_array( $ref[ $k ] ) ) return;
            $ref = &$ref[ $k ];
        }
        unset( $ref[ $last ] );
    }

    # --- сохранение: бэкап + атомарная запись через Json ---
    public function save ( ) : void {
        $backup = $this->path . '.bak';
        @copy( $this->path , $backup );

        Json::writeAtomic( $this->path , $this->data );
    }

    # ============================================================
    #   MUTATE — безопасный read-modify-write под lock-файлом.
    #   Гарантирует, что параллельные изменения site.config
    #   не потеряются.
    #
    #   Использование:
    #       Config::mutate( Paths::config() , function ( Config $cfg ) {
    #           $cfg->setKeys( [ 'logic' ] , 'beta.0.1' );
    #       } );
    # ============================================================
    public static function mutate ( string $path , callable $fn ) : void {
        $lock_path = $path . '.lock';
        $lock_fp   = @fopen( $lock_path , 'cb' );

        if ( $lock_fp === false ) {
            throw new RuntimeException( "Cannot open lock: {$lock_path}" );
        }

        if ( ! flock( $lock_fp , LOCK_EX ) ) {
            fclose( $lock_fp );
            throw new RuntimeException( "Cannot lock: {$lock_path}" );
        }

        try {
            $cfg = new self( $path );
            $fn( $cfg );
            $cfg->save( );
        } finally {
            flock( $lock_fp , LOCK_UN );
            fclose( $lock_fp );
        }
    }
}