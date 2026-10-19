<?PHP
// engines/alogic/alogic_bridge.php

/**
 * Точка входа для нового движка маршрутизации.
 * Отвечает за поиск маршрута, построение цепочки алгоритмов (algoritm -> algoritm_lang -> algoritm_lang_index)
 * и вызов нужного метода (guest, logged или ajax) для рендера.
 */
function alogic_resolve( $main , $static_domain , $static_allias ) {
    require_once __DIR__ . '/bootstrap.php';
    
    // 1. Ищем routes.php в аллиасе (локальные маршруты)
    $logic_ver = $main->main_settings->get('logic');
    $domain = $main->main_settings->domain_real_name;
    $local_routes = dirname(dirname(__DIR__)) . "/app/{$logic_ver}/domain/{$domain}/allias/{$static_allias}/routes.php";
    
    if ( file_exists( $local_routes ) ) {
        $router = require_once $local_routes;
    } else {
        // Фоллбэк на глобальные маршруты, если в аллиасе нет своего файла
        $router = require_once __DIR__ . '/routes.php'; 
    }
    
    // 2. Ищем совпадение по текущему URL
    $uri = $_SERVER[ 'REQUEST_URI' ] ?? '/';
    $match = $router->match( $uri );
    
    if ( $match === null ) {
        // Маршрут не найден — отдаем 404
        alogic_error( $main , $static_domain , $static_allias , 404 );
        return;
    }
    
    $handler = $match[ 'handler' ]; // Например: algoritm_lang_index
    $params  = $match[ 'params'  ]; // Параметры из URL: ['lang' => 'ru-ru', 'login' => 'user']
    
    // 3. Загружаем схему (каркас страницы) до вызова алгоритмов
    $scheme = $main->alliasscheme;
    $scheme->load( 'visuo' );

    // 4. Строим цепочку наследования на основе имени класса
    // algoritm_lang_index -> ['algoritm', 'algoritm_lang', 'algoritm_lang_index']
    $parts = explode( '_' , str_replace( 'algoritm_' , '' , $handler ) );
    $chain = [ 'algoritm' ];
    $current = 'algoritm';
    
    foreach ( $parts as $part ) {
        if ( $part === '' ) continue;
        $current .= '_' . $part;
        $chain[] = $current;
    }

    // 5. Проходим по цепочке и запускаем каждый узел
    foreach ( $chain as $class_name ) {
        $full_class = $static_domain . '\\' . $static_allias . '\\' . $class_name;
        
        // Если класс из цепочки не существует — 404
        if ( ! class_exists( $full_class ) ) {
            alogic_error( $main , $static_domain , $static_allias , 404 );
            return;
        }
        
        // Создаем экземпляр
        $instance = new $full_class( $main , $params );
        
        // Загружаем данные из .data.json (права доступа, флаги и т.д.)
        $reflection = new ReflectionClass($instance);
        $data_file = preg_replace('/\.php$/', '.data.json', $reflection->getFileName());
        
        if (file_exists($data_file)) {
            $data = json_decode(file_get_contents($data_file), true)['default'] ?? [];
            $instance->available = $data['available'] ?? [];
            $instance->auth      = $data['auth'] ?? false;
            $instance->search    = $data['search'] ?? true;
            $instance->default   = $data['default'] ?? false;
        }

        // Проверка авторизации (если требуется auth, а пользователь гость)
        if ( $instance->auth === true && $main->is_guest() ) {
            $auth_class = $static_domain . '\\' . $static_allias . '\\algoritm_auth';
            if ( class_exists( $auth_class ) ) {
                $auth_instance = new $auth_class( $main , $params );
                $main->algoritm = $auth_instance;
                $auth_instance->release();
                $scheme->reload();
                return;
            }
        }

        // Сохраняем текущий узел в main и запускаем release (для сбора данных)
        $main->algoritm = $instance;
        $instance->release();
    }
    
    // 6. ФИНАЛЬНЫЙ ВЫЗОВ РЕНДЕРА (с поддержкой AJAX)
    // Проверяем, пришел ли запрос от JavaScript (режим SPA)
    if ( $main->algoritm->is_ajax() ) {
        $main->algoritm->ajax(); // Вызовет ajax_guest или ajax_logged
    } else {
        // Обычная загрузка страницы (режимы 1 и 2)
        if ( $main->is_guest() ) {
            $main->algoritm->guest();
        } else {
            $main->algoritm->logged();
        }
    }
    
    // 7. Финализируем схему (рендерим HTML или JSON)
    $scheme->reload( );
}

/**
 * Обработчик ошибок для alogic (404 и др.)
 */
function alogic_error( $main , $static_domain , $static_allias , $code = 404 ) {
    http_response_code( $code );
    
    $scheme = $main->alliasscheme;
    if ( $scheme ) $scheme->load( 'visuo' );

    $logic_ver = $main->main_settings->get('logic');
    $domain = $main->main_settings->domain_real_name;
    
    $base_dir = dirname(dirname(__DIR__)) . "/app/{$logic_ver}/domain/{$domain}/allias/";
    
    // Ищем страницу ошибки: сначала в текущем аллиасе, потом в www
    $current_path = $base_dir . $static_allias . '/algoritm/algoritm_error.php';
    $current_class = $static_domain . '\\' . $static_allias . '\\algoritm_error';
    
    $www_path = $base_dir . 'www/algoritm/algoritm_error.php';
    $www_class = $static_domain . '\\www\\algoritm_error';

    if ( file_exists( $current_path ) ) {
        require_once $current_path;
        $final_class = $current_class;
    } elseif ( file_exists( $www_path ) ) {
        require_once $www_path;
        $final_class = $www_class;
    } else {
        die( '404 Not Found (no error class found)' );
    }
    
    if ( class_exists( $final_class ) ) {
        $instance = new $final_class( $main , [ ] );
        $main->algoritm = $instance;
        $instance->release();
        
        // Если это AJAX-запрос, отдаем JSON с ошибкой
        if ( $instance->is_ajax() ) {
            $instance->ajax();
        } else {
            if ( $main->is_guest() ) {
                $instance->guest();
            } else {
                $instance->logged();
            }
        }
    } else {
        die( '404 Not Found (class not found)' );
    }
    
    if ( $scheme ) $scheme->reload();
}