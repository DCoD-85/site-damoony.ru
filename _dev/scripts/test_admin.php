<?php
// _dev/test_admin.php — Smoke-тест ядра админки

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );

define( 'PROJECT_ROOT' , dirname( __DIR__, 2 ) );
define( 'DS' , DIRECTORY_SEPARATOR );

// Подключаем ядро
require_once PROJECT_ROOT . '/corescripts/_lib/Fs.php';
require_once PROJECT_ROOT . '/corescripts/_lib/Json.php';
require_once PROJECT_ROOT . '/corescripts/_lib/Paths.php';
require_once PROJECT_ROOT . '/corescripts/_lib/Blocks.php';
require_once PROJECT_ROOT . '/corescripts/_lib/Config.php';
require_once PROJECT_ROOT . '/www/admin/_lib/Scan.php';

$pass = 0;
$fail = 0;
$errors = [];

function assertTrue( $condition , string $msg ) {
    global $pass, $fail, $errors;
    if ( $condition ) {
        $pass++;
        echo "\033[32m✔\033[0m $msg\n";
    } else {
        $fail++;
        $errors[] = $msg;
        echo "\033[31m✘\033[0m $msg\n";
    }
}

echo "\n\033[36m=== 1. Тест Scan::layoutsFor (Визуальные контейнеры) ===\033[0m\n";
$layouts = Scan::layoutsFor( 'damoony.ru' );
assertTrue( in_array( 'dt_middle' , $layouts ) , 'dt_middle найден в layouts' );
assertTrue( !in_array( 'dt_top' , $layouts ) , 'dt_top НЕ является layout' );

echo "\n\033[36m=== 2. Тест Scan::fillersFor (Список филлеров) ===\033[0m\n";
$fillers = Scan::fillersFor( 'damoony.ru' , 'www' );
$filler_names = array_column( $fillers , 'name' );

assertTrue( in_array( 'top' , $filler_names ) , 'Блок top присутствует' );
assertTrue( !in_array( 'middle' , $filler_names ) , 'middle отфильтрован из филлеров' );
assertTrue( !in_array( 'script' , $filler_names ) , 'script отфильтрован из филлеров' );

$top_row = null;
foreach ( $fillers as $f ) if ( $f['name'] === 'top' ) $top_row = $f;
assertTrue( $top_row !== null && $top_row['has_base'] === true , 'У блока top есть файл базы' );

echo "\n\033[36m=== 3. Тест Scan::blocksDetailsFor (Глобальные блоки) ===\033[0m\n";
$blocks = Scan::blocksDetailsFor( 'damoony.ru' );
$block_names = array_column( $blocks , 'name' );
assertTrue( !in_array( 'middle' , $block_names ) , 'middle отсутствует в глобальных блоках' );
assertTrue( in_array( 'top' , $block_names ) , 'top присутствует в глобальных блоках' );

echo "\n\033[36m=== 4. Тест Config (Чтение и запись) ===\033[0m\n";
try {
    $cfg = new Config( Paths::config() );
    assertTrue( $cfg->get( 'logic' ) !== null , 'logic прочитан из конфига' );
    assertTrue( is_array( $cfg->get( 'domains_real' ) ) , 'domains_real — массив' );
} catch ( Throwable $e ) {
    assertTrue( false , 'Ошибка чтения Config: ' . $e->getMessage() );
}

echo "\n\033[36m=== 5. Тест дерева страниц ===\033[0m\n";
$base = Paths::algoritm( 'damoony.ru' , 'visuo' );
if ( is_dir( $base ) ) {
    $tree = Scan::pageTree( $base );
    assertTrue( !empty( $tree ) , 'Дерево страниц visuo успешно построено' );
    assertTrue( isset( $tree[0]['class'] ) && $tree[0]['class'] === 'algoritm' , 'Корневой узел дерева = algoritm' );
} else {
    echo "\033[33m⚠\033[0m Пропущено: папка visuo не найдена\n";
}

echo "\n" . str_repeat( '-' , 40 ) . "\n";
if ( $fail === 0 ) {
    echo "\033[32mВСЕ ТЕСТЫ ПРОЙДЕНЫ ($pass)\033[0m\n";
    exit( 0 );
} else {
    echo "\033[31mПРОВАЛЕНО: $fail из " . ($pass + $fail) . "\033[0m\n";
    exit( 1 );
}
