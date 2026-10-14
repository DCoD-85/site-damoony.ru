<?PHP

class FillerNew {

    public static function plan ( array $params ) : array {
        return [
            'name'   => $params[ 'name' ] ?? '' ,
            'block'  => $params[ 'block' ] ?? '' ,
            'domain' => $params[ 'domain' ] ?? 'damoony.ru' ,
            'allias' => $params[ 'allias' ] ?? '' ,
        ];
    }

    public static function run ( array $params , ?Context $ctx = null ) : Result {
        $r = new Result( );

        $name   = (string) ( $params[ 'name' ] ?? '' );
        $block  = (string) ( $params[ 'block' ] ?? '' );
        $domain = (string) ( $params[ 'domain' ] ?? 'damoony.ru' );
        $allias = (string) ( $params[ 'allias' ] ?? '' );

        if ( $err = Validate::allias_name( $block ) ) {
            $r->step( 'Валидация блока' , false , $err );
            return $r;
        }
        if ( $err = Validate::allias_name( $allias ) ) {
            $r->step( 'Валидация аллиаса' , false , "--allias обязателен" );
            return $r;
        }
        if ( $err = Validate::domain( $domain ) ) {
            $r->step( 'Валидация домена' , false , $err );
            return $r;
        }

        $domain_path = Paths::domain( $domain );
        if ( ! is_dir( $domain_path ) ) {
            $r->error( "Домен не найден: {$domain_path}" );
            return $r;
        }

        $block_path = Paths::block( $domain , $block );
        if ( ! is_dir( $block_path ) ) {
            $r->error( "Блок не найден: {$block_path}" );
            return $r;
        }

        $ns = Paths::ns( $domain );
        if ( $ns === null ) {
            $r->error( "Не найден domains_namespace для {$domain}" );
            return $r;
        }

        # Путь: <block>_<allias>.php
        $filler_php  = $block_path . '/' . $block . '_' . $allias . '.php';
        $filler_data = $block_path . '/' . $block . '_' . $allias . '.data.php';

        if ( Fs::exists( $filler_php ) ) {
            $r->error( "Filler уже существует: {$filler_php}" );
            return $r;
        }

        $vars = [
            'name'      => $allias ,
            'block'     => $block ,
            'domain'    => $domain ,
            'domain_ns' => $ns ,
        ];

        $src_php  = CS_TPL . '/filler/block.php.tpl';
        $src_data = CS_TPL . '/filler/block.data.php.tpl';

        if ( ! is_file( $src_php ) || ! is_file( $src_data ) ) {
            $r->error( 'Шаблоны filler не найдены в corescripts/templates/filler/' );
            return $r;
        }

        Fs::write( $filler_php ,  Template::render_str( file_get_contents( $src_php ) , $vars ) );
        $r->file( $filler_php );
        $ctx?->step( 'Создан filler' , true , Fs::rel( $filler_php ) );

        Fs::write( $filler_data , Template::render_str( file_get_contents( $src_data ) , $vars ) );
        $r->file( $filler_data );
        $ctx?->step( 'Создан filler.data' , true , Fs::rel( $filler_data ) );

        # lint
        foreach ( [ $filler_php , $filler_data ] as $full ) {
            if ( $err = Template::lint( $full ) ) {
                $r->warn( "Lint: " . Fs::rel( $full ) . ": {$err}" );
            }
        }

        return $r;
    }
}