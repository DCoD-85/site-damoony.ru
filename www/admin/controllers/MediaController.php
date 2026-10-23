<?PHP

class MediaController extends BaseController {

    public function list(): void {
        $path = trim( (string) $this->get('path'), '/' );
        $domain = (string) $this->get('domain');
        
        if ( $path === '' && $domain !== '' ) {
            $path = Paths::real( $domain );
        }
        if ( $domain === '' && $path !== '' ) {
            $parts = explode( '/', $path );
            $possible_domain = $parts[0];
            $cfg = new Config( Paths::config() );
            if ( $cfg->getKeys( [ 'domains_real', $possible_domain ] ) !== null ) {
                $domain = $possible_domain;
            }
        }

        if ( str_contains( $path, '..' ) || str_contains( $path, '\\' ) ) {
            $this->redirect( '?p=media:list' );
        }

        $root = Paths::mediaRoot();
        $current = $path === '' ? $root : $root . '/' . $path;
        
        $real_root = realpath( $root );
        $real_curr = realpath( $current );
        if ( $real_curr === false || strpos( $real_curr, $real_root ) !== 0 ) {
            $this->redirect( '?p=media:list' );
        }

        $dirs = []; $files = [];
        foreach ( scandir( $current ) as $name ) {
            if ( $name === '.' || $name === '..' || $name[0] === '.' ) continue;
            
            $full = $current . '/' . $name;
            $rel  = $path === '' ? $name : $path . '/' . $name;
            
            if ( is_dir( $full ) ) {
                $dirs[] = [ 'name' => $name, 'path' => $rel ];
            } else {
                $files[] = [
                    'name' => $name, 'path' => $rel,
                    'size' => filesize( $full ), 'time' => filemtime( $full ),
                    'url'  => '/media/' . $rel,
                ];
            }
        }
        usort( $dirs, fn( $a, $b ) => strcmp( $a['name'], $b['name'] ) );
        usort( $files, fn( $a, $b ) => strcmp( $a['name'], $b['name'] ) );

        $this->render( 'media_list', [
            'domain' => $domain, 'path' => $path, 'dirs' => $dirs, 'files' => $files,
            'saved'  => ! empty( $this->get('saved') ), 
            'error'  => $this->get('error'),
            'meta'   => [ 'title' => 'Медиа', 'breadcrumbs' => [ ['label' => 'Домены', 'url' => '?p=dashboard'], ['label' => 'Медиа', 'url' => '?p=media:list'] ], 'tabs' => ['show' => true, 'domain' => true, 'active_domain' => 'media'] ]
        ] );
    }

    public function upload(): void {
        $this->requirePost();
        $target_path = trim( (string) $this->post('target_path'), '/' );
        $domain      = (string) $this->post('domain');
        
        if ( str_contains( $target_path, '..' ) ) { $this->redirect( '?p=media:list' ); }
        
        $root = Paths::mediaRoot();
        $dir  = $target_path === '' ? $root : $root . '/' . $target_path;
        
        Fs::mkdir( $dir );
        $real_root = realpath( $root );
        $real_dir  = realpath( $dir );
        if ( $real_dir === false || strpos( $real_dir, $real_root ) !== 0 ) {
            $this->redirect( '?p=media:list' );
        }

        require_once CS_CMD . '/MediaUpload.php';
        $res = MediaUpload::run([ 'dir' => $dir, 'file' => $_FILES['file'] ?? null ]);
        AuditLog::action_ok( 'media:upload', [ 'path' => $target_path, 'file' => $_FILES['file']['name'] ?? '', 'ok' => $res->ok ] );
        
        $back = '?p=media:list' . ( $target_path !== '' ? '&path=' . urlencode( $target_path ) : '' ) . ( $domain !== '' ? '&domain=' . urlencode( $domain ) : '' );
        $this->redirect( $back . ( $res->ok ? '&saved=1' : '&error=' . urlencode( $res->errors[0] ?? 'Ошибка' ) ) );
    }

    public function delete(): void {
        $this->requirePost();
        $path   = trim( (string) $this->post('path'), '/' );
        $domain = (string) $this->post('domain');
        
        if ( str_contains( $path, '..' ) ) { $this->redirect( '?p=media:list' ); }
        
        $root = Paths::mediaRoot();
        $full = $root . '/' . $path;
        
        $real_root = realpath( $root );
        $real_full = realpath( $full );
        if ( $real_full === false || strpos( $real_full, $real_root ) !== 0 || is_dir( $real_full ) ) {
            $this->redirect( '?p=media:list' );
        }
        
        unlink( $real_full );
        AuditLog::action_ok( 'media:delete', [ 'path' => $path ] );
        
        $parent = dirname( $path );
        $parent = $parent === '.' ? '' : $parent;
        $back = '?p=media:list' . ( $parent !== '' ? '&path=' . urlencode( $parent ) : '' ) . ( $domain !== '' ? '&domain=' . urlencode( $domain ) : '' );
        $this->redirect( $back );
    }
}