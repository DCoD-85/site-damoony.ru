<?PHP ?>
<div class="hint" style="margin-bottom:15px; color:#666;">
    Папка медиа: <code>www/media/<?= htmlspecialchars( Paths::real( $domain ) ) ?>/</code> (Загрузка файлов появится в следующем обновлении).
</div>

<?PHP if ( empty( $files ) ) : ?>
    <p>Файлов нет.</p>
<?PHP else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Файл</th>
                <th>Размер</th>
                <th>Дата изменения</th>
            </tr>
        </thead>
        <tbody>
            <?PHP foreach ( $files as $f ) : ?>
                <tr>
                    <td><code><?= htmlspecialchars( $f['name'] ) ?></code></td>
                    <td><?= round( $f['size'] / 1024 , 1 ) ?> KB</td>
                    <td><?= date( 'd.m.Y H:i' , $f['time'] ) ?></td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>
<?PHP endif ?>