<?PHP

class DashboardController extends BaseController {
    public function index(): void {
        $cfg = new Config( Paths::config() );
        $domains = (array) $cfg->get( 'domains_real' );
        $real_domains = [];
        $aliases = [];
        foreach ( $domains as $d => $real ) {
            if ( $d === $real ) $real_domains[ $d ] = true;
            else $aliases[ $real ][] = $d;
        }
        $rows = [];
        foreach ( array_keys( $real_domains ) as $real ) {
            $alliases = [];
            foreach ( (array) $cfg->getKeys( [ 'alliases_real', $real ], [] ) as $a => $t ) {
                if ( $a !== '' ) $alliases[] = $a;
            }
            $rows[] = [
                'domain' => $real,
                'ns' => $cfg->getKeys( [ 'domains_namespace', $real ] ) ?? '—',
                'avail' => ! empty( $cfg->getKeys( [ 'domains_available', $real ] ) ),
                'fs' => is_dir( Paths::domain( $real ) ),
                'stub' => ! empty( $cfg->getKeys( [ 'domains_stub', $real, 'enabled' ] ) ),
                'alliases' => $alliases,
                'aliases' => $aliases[ $real ] ?? []
            ];
        }
        $this->render( 'dashboard', [
            'domains' => $rows,
            'meta' => [
                'title' => 'Домены',
                'actions' => '<a href="?p=stubs:list" class="btn-edit">Библиотека заглушек</a> <a href="?p=domain:new" class="btn-primary">+ Добавить домен</a>'
            ]
        ]);
    }
}