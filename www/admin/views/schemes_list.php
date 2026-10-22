<?PHP ?>
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
        <?PHP foreach ( $schemes as $s ) : 
            $data_file = Paths::domain( $domain ) . '/alliasscheme/alliasscheme_' . $s . '.data.php';
            $has_data = is_file( $data_file );
        ?>
            <tr>
                <td><b><?= htmlspecialchars( $s ) ?></b></td>
                <td>✓</td>
                <td><?= $has_data ? '✓' : '✗ (нет)' ?></td>
                <td>
                    <div class="td-actions">
                        <?PHP if ( $has_data ) : ?>
                            <a href="?p=scheme:edit&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-edit">Ред.</a>
                            <a href="?p=scheme:copy&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-edit">Копировать</a>
                            <?PHP if ( ! in_array( $s , [ 'www' , 'visuo' , 'error' ] ) ) : ?>
                                <a href="?p=scheme:remove&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $s ) ?>" class="btn-danger" onclick="return confirm('Удалить схему <?= htmlspecialchars( $s ) ?>?')">Удалить</a>
                            <?PHP endif ?>
                        <?PHP else : ?>
                            <span class="btn-cancel" style="opacity:0.5; cursor:not-allowed;">Нет data.php</span>
                        <?PHP endif ?>
                    </div>
                </td>
            </tr>
        <?PHP endforeach ?>
    </tbody>
</table>