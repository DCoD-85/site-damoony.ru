<?PHP

return [
    'title' => 'tree (движок)',

    'setup' => function ( ) {
        // Загружаем сам класс tree
        require_once SOURCES . '/tree.php';
    },

    'tests' => [
        'match: пустой URL → default' => function ( ) {
            $tree = new tree( new main() );
            $m = new ReflectionMethod( 'tree', 'match' );
            $m->setAccessible( true );
            
            [ $target, $arg ] = $m->invoke( $tree, '', [ '' ] );
            Assert::same( '', $target );
            Assert::null( $arg );
        },

        'match: |list для пустого' => function ( ) {
            $tree = new tree( new main() );
            $m = new ReflectionMethod( 'tree', 'match' );
            $m->setAccessible( true );
            
            [ $target, $arg ] = $m->invoke( $tree, '', [ '|list' ] );
            Assert::same( 'list', $target );
        },

        'match: regex slug' => function ( ) {
            $tree = new tree( new main() );
            $m = new ReflectionMethod( 'tree', 'match' );
            $m->setAccessible( true );
            
            [ $target, $arg ] = $m->invoke( $tree, 'dsadas', [ '[a-z0-9-]+|slug' ] );
            Assert::same( 'slug', $target );
            Assert::same( 'slug', $arg );
        },

        'match: строгое совпадение' => function ( ) {
            $tree = new tree( new main() );
            $m = new ReflectionMethod( 'tree', 'match' );
            $m->setAccessible( true );
            
            [ $target, $arg ] = $m->invoke( $tree, 'about', [ 'about', 'contact' ] );
            Assert::same( 'about', $target );
            Assert::null( $arg );
        },
    ],
];