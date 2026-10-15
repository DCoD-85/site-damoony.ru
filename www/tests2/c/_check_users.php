<?php
header( 'Content-Type: text/plain; charset=utf-8' );

$cfg  = json_decode( file_get_contents( __DIR__ . '/../../../secure/db.config' ) , true );
$base = null;
foreach ( $cfg[ 'bases' ] as $b ) {
    if ( $b[ 'localname' ] === 'login_damoony' ) { $base = $b; break; }
}
if ( ! $base ) die( 'no config' );

$m = new mysqli( $base[ 'dbhost' ] , $base[ 'username' ] , $base[ 'password' ] , $base[ 'dbname' ] );
if ( $m->connect_errno ) die( 'connect: ' . $m->connect_error );

$r = $m->query( 'SELECT id, login, password, LENGTH(password) AS len, status FROM toolsusers LIMIT 10' );
if ( ! $r ) die( 'query: ' . $m->error );

while ( $row = $r->fetch_assoc() ) {
    echo json_encode( $row , JSON_UNESCAPED_UNICODE ) . PHP_EOL;
}