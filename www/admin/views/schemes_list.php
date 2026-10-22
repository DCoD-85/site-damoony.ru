<div class="tabs-wrapper">
    <?php $active_tab = 'schemes'; $actions = '<a href="?p=scheme:new&domain=' . urlencode($domain) . '" class="btn-edit">+ Добавить схему</a>'; require __DIR__ . '/_domain_tabs.php'; ?>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Схема</th>
            <th>Файл</th>
            <th>Data (редактор)</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $schemes as $s ) : 
            $data_file = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $s . '.data.php';
            $has_data = is_file( $data_file );
        ?>
            <tr>
                <td><b><?= htmlspecialchars( $s ) ?></b></td>
                <td>✓</td>
                <td><?= $has_data ? '✓' : '✗ (нет)' ?></td>
                <td>
                    <div class="td-actions">
                        <?php if ( $has_data ) : ?>
                            <a href="?p=scheme:edit&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-edit">Ред.</a>
                        <?php else : ?>
                            <span class="btn-cancel" style="opacity:0.5; cursor:not-allowed;">Нет data.php</span>
                        <?php endif ?>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
