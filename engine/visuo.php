<?PHP
// engine/visuo.php

$visuo_path = dirname( __DIR__ ) . DS . 'visuo' . DS . 'visuo.php';
( include_once $visuo_path ) or error_file( $visuo_path , __FILE__ , __LINE__ );