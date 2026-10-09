<?php
echo password_hash( 'qwezxcasd123' , PASSWORD_DEFAULT );

require __DIR__ . '/../../../www/main.php';  // или просто проверь без bootstrap

$hash = 'ВСТАВЬ_ХЕШ_ИЗ_БД_ЦЕЛИКОМ';
$pwd  = 'ВСТАВЬ_ПАРОЛЬ_КОТОРЫЙ_ВВОДИШЬ';

print '<div>vardump: ';

var_dump( password_verify( $pwd , $hash ) );

print '</div>';