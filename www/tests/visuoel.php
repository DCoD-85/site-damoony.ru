<?PHP
# Visuoel tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root_dir = dirname( __DIR__ , 2 );

require $root_dir . '/sources/visuo/sources/visuoel.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group.php';
require $root_dir . '/sources/visuo/sources/visuoel/visuoel_group_element.php';

echo '<h1>Visuoel tests</h1>';

echo '<h2>1. class_exists visuoel</h2>';
echo '<b>' . var_export( class_exists( 'visuoel' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>2. class_exists visuoel_group</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_group' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>3. class_exists visuoel_group_element</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_group_element' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>4. class_exists visuoel__data</h2>';
echo '<b>' . var_export( class_exists( 'visuoel__data' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>5. class_exists visuoel_group__data</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_group__data' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>6. class_exists visuoel_group_element__data</h2>';
echo '<b>' . var_export( class_exists( 'visuoel_group_element__data' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>7. visuoel_group extends visuoel</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_group' , 'visuoel' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>8. visuoel_group_element extends visuoel</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_group_element' , 'visuoel' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>9. visuoel_group__data extends visuoel__data</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_group__data' , 'visuoel__data' ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>10. visuoel_group_element__data extends visuoel__data</h2>';
echo '<b>' . var_export( is_subclass_of( 'visuoel_group_element__data' , 'visuoel__data' ) , true ) . '</b> (ожидаем true)<br>';

# --- root ---
$root = new visuoel( );
echo '<h2>11. root->parent === null</h2>';
echo '<b>' . var_export( $root->parent , true ) . '</b> (ожидаем NULL)<br>';

echo '<h2>12. root->visuoel === root</h2>';
echo 'same: <b>' . var_export( $root->visuoel === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>13. root->is_root() === true</h2>';
echo '<b>' . var_export( $root->is_root( ) , true ) . '</b> (ожидаем true)<br>';

echo '<h2>14. root->css_class() === "visuoel"</h2>';
echo '<b>' . $root->css_class( ) . '</b> (ожидаем visuoel)<br>';

echo '<h2>15. root->data() instanceof visuoel__data</h2>';
echo 'class: <b>' . get_class( $root->data( ) ) . '</b> (ожидаем visuoel__data)<br>';

echo '<h2>16. root->data()->node === root</h2>';
echo 'same: <b>' . var_export( $root->data( )->node === $root , true ) . '</b> (ожидаем true)<br>';

# --- group ---
$group = new visuoel_group( $root );
echo '<h2>17. group->parent === root</h2>';
echo 'same: <b>' . var_export( $group->parent === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>18. group->visuoel === root</h2>';
echo 'same: <b>' . var_export( $group->visuoel === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>19. group->is_root() === false</h2>';
echo '<b>' . var_export( $group->is_root( ) , true ) . '</b> (ожидаем false)<br>';

echo '<h2>20. group->css_class() === "group"</h2>';
echo '<b>' . $group->css_class( ) . '</b> (ожидаем group)<br>';

echo '<h2>21. group->data() instanceof visuoel_group__data</h2>';
echo 'class: <b>' . get_class( $group->data( ) ) . '</b> (ожидаем visuoel_group__data)<br>';

# --- element ---
$element = new visuoel_group_element( $group );
echo '<h2>22. element->parent === group</h2>';
echo 'same: <b>' . var_export( $element->parent === $group , true ) . '</b> (ожидаем true)<br>';

echo '<h2>23. element->visuoel === root</h2>';
echo 'same: <b>' . var_export( $element->visuoel === $root , true ) . '</b> (ожидаем true)<br>';

echo '<h2>24. element->css_class() === "element"</h2>';
echo '<b>' . $element->css_class( ) . '</b> (ожидаем element)<br>';

echo '<h2>25. element->data() instanceof visuoel_group_element__data</h2>';
echo 'class: <b>' . get_class( $element->data( ) ) . '</b> (ожидаем visuoel_group_element__data)<br>';

echo '<h2>26. element->data()->readonly === false</h2>';
echo '<b>' . var_export( $element->data( )->readonly , true ) . '</b> (ожидаем false)<br>';

echo '<h2>27. element->data()->disabled === false</h2>';
echo '<b>' . var_export( $element->data( )->disabled , true ) . '</b> (ожидаем false)<br>';

# --- init ---
echo '<h2>28. init() sets flag</h2>';
$root->init( );
echo 'root->init: <b>' . var_export( $root->init , true ) . '</b> (ожидаем true)<br>';

# --- custom __data ---
$custom = new visuoel_group_element__data( $element );
$custom->readonly = true;
$element2 = new visuoel_group_element( $group , $custom );
echo '<h2>29. custom __data attached (constructor)</h2>';
echo 'same: <b>' . var_export( $element2->data( ) === $custom , true ) . '</b> (ожидаем true)<br>';

echo '<h2>30. custom __data readonly propagated</h2>';
echo '<b>' . var_export( $element2->data( )->readonly , true ) . '</b> (ожидаем true)<br>';

echo '<h2>31. tag() === "div"</h2>';
echo '<b>' . $root->tag( ) . '</b> (ожидаем div)<br>';

echo '<h2>32. base visuoelement NOT touched: class_exists visuoelement</h2>';
echo '<b>' . var_export( class_exists( 'visuoelement' ) , true ) . '</b> (ожидаем false — isolated, visuoelement не подключён)<br>';

# --- реальная разметка ---
echo '<hr>';
echo '<h2>Демо: дерево visuoel в HTML</h2>';
echo '<div class="' . htmlspecialchars( $root->css_class( ) , ENT_QUOTES ) . '">';
echo   '<div class="' . htmlspecialchars( $group->css_class( ) , ENT_QUOTES ) . '">';
echo     '<div class="' . htmlspecialchars( $element->css_class( ) , ENT_QUOTES ) . '">element content</div>';
echo   '</div>';
echo '</div>';
echo '<p>Ожидаем: <b>visuoel → group → element</b> (вложенные div\'ы).</p>';

echo '<hr>done';