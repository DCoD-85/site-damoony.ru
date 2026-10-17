<?PHP

# engine/alogic_bridge.php

function alogic_resolve( $main ) {
    // Загружаем роутер
    $router = require_once dirname( __DIR__ ) . '/alogic/routes.php';
    
    // Получаем текущий URL
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    
    // Ищем совпадение
    $match = $router->match( $uri );
    
    if ( $match === null ) {
        http_response_code( 404 );
        // Используем стандартный обработчик ошибок движка, если он есть
        if ( function_exists( 'error' ) ) {
            error( '404 Not Found' , 'Страница не найдена: ' . htmlspecialchars( $uri ) );
        } else {
            echo '404 Not Found';
        }
        return;
    }
    
    $handler = $match[ 'handler' ];
    $params  = $match[ 'params' ];
    
    // Проверяем, существует ли класс
    if ( ! class_exists( $handler ) ) {
        http_response_code( 500 );
        error( '500 Internal Error' , "Класс алгоритма не найден: {$handler}" );
        return;
    }
    
    // Создаем и запускаем алгоритм
    // (Предполагаем, что алгоритмы наследуют algoritm_ и имеют метод algoritm())
    $instance = new $handler( $main , $params );
    
    if ( method_exists( $instance , 'algoritm' ) ) {
        $instance->algoritm();
    } elseif ( method_exists( $instance , 'start' ) ) {
        $instance->start();
    }
}