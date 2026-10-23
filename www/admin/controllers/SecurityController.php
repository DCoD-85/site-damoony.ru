<?PHP

class SecurityController extends BaseController {

    public function index(): void {
        $tab = (string) $this->get('tab', 'policy');
        if ( ! in_array( $tab, [ 'policy', 'users', 'logs' ], true ) ) $tab = 'policy';
        $saved = ! empty( $this->get('saved') );
        $generated_invite = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $action = (string) $this->post('action');
            try {
                switch ( $action ) {
                    case 'save_policy' :
                        $cur = Security::get_config();
                        $cur['mode'] = ( $this->post('mode', 'whitelist') === 'blacklist' ) ? 'blacklist' : 'whitelist';
                        $cur['require_fingerprint'] = ! empty( $this->post('require_fingerprint') );
                        Security::save_config( $cur );
                        AuditLog::action_ok( 'security:policy', [ 'mode' => $cur['mode'] ] );
                        $this->redirect( '?p=security&tab=policy&saved=1' );

                    case 'reset_devices' :
                        $login = (string) $this->post('login');
                        $self  = (string) ( $_SESSION['admin_login'] ?? '' );
                        if ( $login !== '' ) {
                            $ok = Security::reset_user_devices( $login, $self );
                            if ( $ok ) {
                                AuditLog::action_ok( 'security:reset_devices', [ 'login' => $login ] );
                            } else {
                                AuditLog::action_ok( 'security:reset_devices_blocked', [ 'login' => $login, 'self' => $self ] );
                            }
                        }
                        $this->redirect( '?p=security&tab=users&saved=1' );

                    case 'create_invite' :
                        $invite_type = (string) $this->post('invite_type', 'new_user');
                        $target_user = null;
                        
                        if ( $invite_type === 'bind_user' ) {
                            $target_user = (string) $this->post('target_user');
                            $role = 'bind_only';
                        } else {
                            $role = (string) $this->post('role', 'admin');
                            if ( ! in_array( $role, [ 'admin', 'super' ], true ) ) $role = 'admin';
                        }
                        
                        $token = Security::create_invite( $role, null, $target_user );
                        $scheme = ( ( $_SERVER['HTTPS'] ?? '' ) === 'on' ) ? 'https' : 'http';
                        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
                        $generated_invite = $scheme . '://' . $host . '/admin/?token=' . $token;
                        AuditLog::action_ok( 'security:invite_create', [ 'role' => $role, 'target' => $target_user, 'token' => substr( $token, 0, 8 ) ] );
                        break;

                    case 'revoke_invite' :
                        $token = (string) $this->post('token');
                        if ( $token !== '' ) {
                            Security::revoke_invite( $token );
                            AuditLog::action_ok( 'security:invite_revoke', [ 'token' => substr( $token, 0, 8 ) ] );
                        }
                        $this->redirect( '?p=security&tab=users&saved=1' );
                }
            } catch ( Throwable $e ) { error_log( 'security action error: ' . $e->getMessage() ); }
        }

        $sec_cfg = Security::get_config();
        $admins  = Security::get_data();
        $logs = [];
        $log_path = PROJECT_ROOT . '/secure/logs/security.log';

        if ( is_file( $log_path ) ) {
            $lines = @file( $log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
            if ( is_array( $lines ) ) {
                foreach ( array_slice( array_reverse( $lines ), 0, 200 ) as $line ) {
                    $row = json_decode( $line, true );
                    $logs[] = is_array( $row ) ? $row : [ 'ts' => '', 'ip' => '', 'event' => '?', 'data' => [] ];
                }
            }
        }

        $this->render( 'security', [
            'tab' => $tab, 'saved' => $saved, 'generated_invite' => $generated_invite, 'sec_cfg' => $sec_cfg, 'admins' => $admins, 'logs' => $logs,
            'meta' => [ 'title' => 'Безопасность', 'breadcrumbs' => [ [ 'label' => 'Настройки', 'url' => '?p=settings' ], [ 'label' => 'Безопасность', 'url' => '' ] ] ]
        ] );
    }
}