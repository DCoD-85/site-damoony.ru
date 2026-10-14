<?php
define( 'PROJECT_ROOT' , dirname( __DIR__ ) );

require_once __DIR__ . '/_lib/Paths.php';

echo "logic: "      . Paths::logic()                 . "\n";
echo "real(d2): "   . Paths::real('damoony2.ru')     . "\n";
echo "real(d): "    . Paths::real('damoony.ru')      . "\n";
echo "ns(d2): "     . Paths::ns('damoony2.ru')       . "\n";
echo "ns(hert): "   . Paths::ns('hert.online')       . "\n";
echo "domain(d2): " . Paths::domain('damoony2.ru')   . "\n";
echo "domain(new): " . Paths::domain('example.com')  . "\n";