<?PHP

class PageNew {

    public static function plan ( array $params ) : array {
        return [
            'name'   => $params[ 'name' ]   ?? '' ,
            'parent' => $params[ 'parent' ] ?? '' ,
            'domain' => $params[ 'domain' ] ?? 'damoony.ru' ,
            'allias' => $params[ 'allias' ] ?? 'www' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        # 1. имя страницы
        $name = (string) ( $params[ 'name' ] ?? '' );
        if ( $err = Validate::allias_name( $name ) ) {
            $r->step( 'Валидация имени' , false , $err );
            return $r;
        }
        $r->step( 'Валидация имени' , true , $name );

        # 2. домен / аллиас
        $domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
        $allias = (string) ( $params[ 'allias' ] ?? 'www' );

        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }
        if ( $err = Validate::allias_name( $allias ) ) {
            $r->step( 'Валидация аллиаса' , false , $err );
            return $r;
        }

        $allias_path = Paths::allias( $domain , $allias );
        if ( ! is_dir( $allias_path ) ) {
            $r->error( "Аллиас не найден: {$allias_path}" );
            return $r;
        }
        $r->step( 'Домен / аллиас' , true , "{$domain} / {$allias}" );

        # 3. parent
        $parent = trim( (string) ( $params[ 'parent' ] ?? '' ) , '/' );
        if ( $parent !== '' ) {
            if ( $err = Validate::page_path( $parent ) ) {
                $r->error( "Некорректный путь родителя: {$err}" );
                return $r;
            }
        }

        # 4. namespace домена
        $ns = Paths::ns( $domain );
        if ( $ns === null ) {
            $r->error( "Не найден domains_namespace для {$domain}" );
            return $r;
        }
        $r->step( 'Namespace' , true , $ns );

        # 5. пути
        $base        = $allias_path . '/algoritm';
        $parent_dir  = $parent === '' ? $base : $base . '/' . $parent;
        $parent_ns   = $parent === '' ? ''    : str_replace( '/' , '_' , $parent );
        $full_ns     = $parent_ns === '' ? $name : $parent_ns . '_' . $name;

        $page_dir    = $parent_dir . '/' . $name;
        $base_name   = 'algoritm_' . $full_ns;
        $page_file   = $page_dir . '/' . $base_name . '.php';
        $parent_file = $parent === ''
            ? $base . '/algoritm.php'
            : $parent_dir . '/algoritm_' . $parent_ns . '.php';

        # 5.5 пред-валидация до первой записи — чтобы не оставить сироту
        if ( ! is_dir( $parent_dir ) ) {
            $r->error( "Родительский каталог не найден: " . Fs::rel( $parent_dir ) );
            return $r;
        }
        if ( ! is_file( $parent_file ) ) {
            $r->error( "Родительский алгоритм не найден: " . Fs::rel( $parent_file ) );
            return $r;
        }
        if ( Fs::exists( $page_file ) ) {
            $r->error( "Страница уже существует: " . Fs::rel( $page_file ) );
            return $r;
        }

        # шаблоны — тоже до записи
        $tpl_php  = CS_TPL . '/page/page.php.tpl';
        $tpl_json = CS_TPL . '/page/page.data.json.tpl';

        if ( ! is_file( $tpl_php ) || ! is_file( $tpl_json ) ) {
            $r->error( 'Шаблоны не найдены в corescripts/templates/page/' );
            return $r;
        }

        # родительский .data.json — если есть, обязан быть валидным
        $parent_json = preg_replace( '/\.php$/' , '.data.json' , $parent_file );
        if ( is_file( $parent_json ) && ! is_array( Json::read( $parent_json ) ) ) {
            $r->error( "Родительский .data.json битый: " . Fs::rel( $parent_json ) );
            return $r;
        }

        # 6. создать .php + .data.json
        $vars_render = [
            'name'      => $full_ns ,
            'UPPER'     => strtoupper( $full_ns ) ,
            'domain'    => $domain ,
            'domain_ns' => $ns ,
            'allias'    => $allias ,
        ];

        Fs::write( $page_file , Template::render_str( file_get_contents( $tpl_php ) , $vars_render ) );
        $r->file( $page_file );
        $r->step( 'Создан файл' , true , Fs::rel( $page_file ) );

        $json_file = $page_dir . '/' . $base_name . '.data.json';
        Fs::write( $json_file , file_get_contents( $tpl_json ) );

        $r->file( $json_file );
        $r->step( 'Создан json' , true , Fs::rel( $json_file ) );

        # 7. patch $available — в родительский .data.json
        if ( ! is_file( $parent_json ) ) {
            $r->warn( 'Родительский .data.json не найден: ' . Fs::rel( $parent_json ) );
        } else {
            $patched = self::patch_available_json( $parent_json , $name );
            if ( $patched ) {
                $r->file( $parent_json );
                $r->step( 'Обновлён $available' , true , Fs::rel( $parent_json ) );
            } else {
                $r->warn( "'{$name}' уже в \$available" );
            }
        }

        # 8. lint
        $lint_errors = 0;
        foreach ( $r->created as $full ) {
            if ( substr( $full , -4 ) !== '.php' ) continue;
            if ( $err = Template::lint( $full ) ) {
                $r->warn( "Lint: " . Fs::rel( $full ) . ": {$err}" );
                $lint_errors++;
            }
        }
        if ( $lint_errors === 0 ) {
            $r->step( 'Синтаксис PHP' , true , 'все файлы OK' );
        }

        return $r;
    }

    # --- patch available (атомарно через Json) ---
    private static function patch_available_json ( string $file , string $name ) : bool {
        $data = Json::read( $file );
        if ( ! is_array( $data ) ) return false;
        if ( ! isset( $data[ 'default' ][ 'available' ] ) ) return false;
        if ( ! is_array( $data[ 'default' ][ 'available' ] ) ) return false;

        if ( in_array( $name , $data[ 'default' ][ 'available' ] , true ) ) return false;

        $data[ 'default' ][ 'available' ][ ] = $name;

        Json::writeAtomic( $file , $data );
        return true;
    }
}