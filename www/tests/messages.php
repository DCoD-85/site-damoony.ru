<?PHP
# Messages tests — isolated
error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

define( 'DS'  , DIRECTORY_SEPARATOR );
define( 'DSA' , '/' );
define( 'GL_LOG' , false );
define( 'GL_DEBUG' , true );

function error ( $t , $m , $f = '' , $l = '' ) { echo "<div style='color:red'>ERROR: $t | $m</div>"; }
function error_file ( $p , $f , $l ) { error( 'File' , $p , $f , $l ); }

$root = dirname( __DIR__ , 2 );

# fake main for skin
class fake_main_settings {
	public function skin ( string $opt ) {
		return $opt === 'skin' ? 'default' : 'www';
	}
}
class main {
	public $main_settings;
	public function __construct ( ) {
		$this->main_settings = new fake_main_settings( );
	}
	public function __get ( $n ) { return null; }
}

# load visuotag (needed for as_tag) — but we can skip actual rendering
# since messages only builds tree. Just define a stub class.
# load visuotag (needed for as_tag) — visuo is not migrated yet,
# so we take it from sources_old until we migrate visuo module
# visuotag stub — для теста messages только
# (полный visuo тестируем отдельно, после миграции модуля)
if ( ! class_exists( 'visuotag' ) ) {
    class visuotag {
        private string $tagname = '';
        private string $content_str = '';
        private array  $children = [ ];
        private array  $attrs = [ ];
        private bool   $closed = true;
        private ?visuotag $parent = null;

        public function __construct ( $visuorelease = null , $vt = null , string $tagname = '' , bool $closed = true ) {
            $this->tagname = $tagname;
            $this->closed = $closed;
            if ( $vt instanceof visuotag ) $this->parent = $vt;
        }

        public function tag ( string $name , bool $closed = true , ...$rest ) : visuotag {
            $child = new visuotag( null , $this , $name , $closed );
            # named args: class, id, style
            if ( isset( $rest[ 'class' ] ) && $rest[ 'class' ] !== '' ) $child->attrs_add( 'class' , $rest[ 'class' ] );
            if ( isset( $rest[ 'id' ] )    && $rest[ 'id' ]    !== '' ) $child->attrs_add( 'id'    , $rest[ 'id' ] );
            if ( isset( $rest[ 'style' ] ) && $rest[ 'style' ] !== '' ) $child->attrs_add( 'style' , $rest[ 'style' ] );
            $this->children[ ] = $child;
            return $child;
        }

        public function content ( string $html ) : visuotag {
            $this->content_str = $html;
            return $this;
        }

        public function attrs_add ( string $name , $value ) : visuotag {
            $this->attrs[ $name ] = (string) $value;
            return $this;
        }

        public function parent ( ) : visuotag {
            return $this->parent ?? $this;
        }

        public function root ( ) : visuotag {
            $node = $this;
            while ( $node->parent !== null ) $node = $node->parent;
            return $node;
        }

        public function append ( $v ) : visuotag {
            return $this;
        }

        public function release ( int $cicle = 0 ) : string {
            if ( $this->tagname === '' ) {
                $out = '';
                foreach ( $this->children as $c ) $out .= $c->release( );
                return $out;
            }
            $attrs = '';
            foreach ( $this->attrs as $k => $v ) {
                $attrs .= ' ' . $k . '="' . htmlspecialchars( $v , ENT_QUOTES ) . '"';
            }
            if ( $this->closed ) {
                $inner = $this->content_str;
                foreach ( $this->children as $c ) $inner .= $c->release( );
                return '<' . $this->tagname . $attrs . '>' . $inner . '</' . $this->tagname . '>';
            }
            return '<' . $this->tagname . $attrs . ' />';
        }
    }
}

require $root . '/sources/messages.php';

echo '<h1>Messages tests</h1>';

$main = new main( );
$msg = new messages( $main );

# 1. empty
echo '<h2>1. Empty state</h2>';
echo 'count: <b>' . $msg->count( ) . '</b><br>';
echo 'has: <b>' . ( $msg->has( ) ? 'true' : 'false' ) . '</b><br>';
echo 'release: <b>' . htmlspecialchars( $msg->release( ) ) . '</b> (empty string expected)<br>';

# 2. add 3
echo '<h2>2. Add 3 messages</h2>';
$msg->add( 'error'   , 'Ошибка'        , 'Вы ввели неверные данные' );
$msg->add( 'success' , 'Успех'         , 'Всё хорошо' );
$msg->add( 'warning' , 'Предупреждение', 'Проверьте поля' );
echo 'count: <b>' . $msg->count( ) . '</b><br>';
echo 'has: <b>' . ( $msg->has( ) ? 'true' : 'false' ) . '</b><br>';

# 3. release → HTML
echo '<h2>3. Release (HTML)</h2>';
echo '<pre>' . htmlspecialchars( $msg->release( ) ) . '</pre>';

# 4. unknown status → console
echo '<h2>4. Unknown status fallback</h2>';
$msg->clear( );
$msg->add( 'hacker_status' , 'X' , 'Y' );
echo 'release: <pre>' . htmlspecialchars( $msg->release( ) ) . '</pre>';
echo 'должен содержать <b>message_console</b> (whitelist)<br>';

# 5. clear
echo '<h2>5. Clear</h2>';
$msg->clear( );
echo 'count: <b>' . $msg->count( ) . '</b><br>';
echo 'release: <b>' . htmlspecialchars( $msg->release( ) ) . '</b><br>';

echo '<hr>done';