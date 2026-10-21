<?php
$active_tab = 'media';
$actions = '';
require __DIR__ . '/_domain_tabs.php';
?>

<div class="hint" style="margin-bottom:15px; color:#666;">
    Папка медиа: <code>www/media/<?= htmlspecialchars( Paths::real( $domain ) ) ?>/</code> (Загрузка файлов появится в следующем обновлении).
</div>

<?php if ( empty( $files ) ) : ?>
    <p>Файлов нет.</p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Файл</th>
                <th>Размер</th>
                <th>Дата изменения</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $files as $f ) : ?>
                <tr>
                    <td><code><?= htmlspecialchars( $f['name'] ) ?></code></td>
                    <td><?= round( $f['size'] / 1024 , 1 ) ?> KB</td>
                    <td><?= date( 'd.m.Y H:i' , $f['time'] ) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
