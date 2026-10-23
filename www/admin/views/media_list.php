<?PHP 
$domain_query = $domain !== '' ? '&domain=' . urlencode($domain) : ''; 
?>

<?PHP if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Загружено</div>
<?PHP endif ?>
<?PHP if ( ! empty( $error ) ) : ?>
    <div class="error-msg">✘ <?= htmlspecialchars( $error ) ?></div>
<?PHP endif ?>

<!-- Хлебные крошки + Кнопка вверх -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div class="breadcrumbs">
        <a href="?p=media:list">Медиа</a>
        <?PHP $parts = $path === '' ? [] : explode( '/' , $path ); $acc = ''; ?>
        <?PHP foreach ( $parts as $part ) : ?>
            <?PHP $acc = $acc === '' ? $part : $acc . '/' . $part; ?>
            <span class="sep">/</span>
            <a href="?p=media:list&path=<?= urlencode( $acc ) ?><?= $domain_query ?>"><?= htmlspecialchars( $part ) ?></a>
        <?PHP endforeach ?>
    </div>
    <?PHP if ( $path !== '' ) : ?>
        <?PHP $parent = dirname( $path ); $parent = $parent === '.' ? '' : $parent; ?>
        <a href="?p=media:list<?= $parent !== '' ? '&path=' . urlencode( $parent ) : '' ?><?= $domain_query ?>" class="btn-cancel">← Наверх</a>
    <?PHP endif ?>
</div>

<!-- Форма загрузки в текущую папку -->
<div class="form" style="margin-bottom: 24px; max-width: 100%;">
    <form method="post" action="?p=media:upload" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center;">
        <?= Csrf::field() ?>
        <input type="hidden" name="target_path" value="<?= htmlspecialchars( $path ) ?>">
        <input type="hidden" name="domain" value="<?= htmlspecialchars( $domain ) ?>">
        <input type="file" name="file" required style="flex:1; padding: 8px; border: 1px solid var(--border); border-radius: 4px; background: #fff;">
        <button type="submit" class="btn-primary">Загрузить сюда</button>
    </form>
    <div class="hint" style="margin-top:10px;">Изображения (jpg, png, gif) автоматически конвертируются в WebP и сжимаются до 1920px.</div>
</div>

<!-- Список папок -->
<?PHP if ( ! empty( $dirs ) ) : ?>
    <table class="table" style="margin-bottom:20px;">
        <thead><tr><th style="width:60%;">Папка</th><th>Перейти</th></tr></thead>
        <tbody>
            <?PHP foreach ( $dirs as $d ) : ?>
                <tr style="cursor:pointer;" onclick="window.location='?p=media:list&path=<?= urlencode( $d['path'] ) ?><?= $domain_query ?>';">
                    <td>📁 <b><?= htmlspecialchars( $d['name'] ) ?></b></td>
                    <td><a href="?p=media:list&path=<?= urlencode( $d['path'] ) ?><?= $domain_query ?>" class="btn-edit">Открыть</a></td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>
<?PHP endif ?>

<!-- Список файлов -->
<?PHP if ( empty( $files ) ) : ?>
    <p>В этой папке нет файлов.</p>
<?PHP else : ?>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 80px; text-align:center;">Превью</th>
                <th>Файл</th>
                <th>Размер</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?PHP foreach ( $files as $f ) : ?>
                <tr>
                    <td style="text-align:center;">
                        <?PHP if ( preg_match('/\.(webp|jpg|jpeg|png|gif|svg)$/i', $f['name']) ) : ?>
                            <a href="<?= htmlspecialchars($f['url']) ?>" target="_blank">
                                <img src="<?= htmlspecialchars($f['url']) ?>" style="max-width: 60px; max-height: 60px; border-radius: 4px; object-fit: cover;" alt="">
                            </a>
                        <?PHP else : ?>
                            <span style="font-size: 28px;">📄</span>
                        <?PHP endif ?>
                    </td>
                    <td>
                        <code><?= htmlspecialchars( $f['name'] ) ?></code><br>
                        <small style="color:var(--text-dim);">
                            <a href="<?= htmlspecialchars($f['url']) ?>" target="_blank">Открыть</a> | 
                            <a href="#" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($f['url']) ?>'); alert('URL скопирован'); return false;">Копировать URL</a>
                        </small>
                    </td>
                    <td><?= round( $f['size'] / 1024 , 1 ) ?> KB</td>
                    <td><?= date( 'd.m.Y H:i' , $f['time'] ) ?></td>
                    <td>
                        <form method="post" action="?p=media:delete" style="display:inline;" onsubmit="return confirm('Удалить файл <?= htmlspecialchars($f['name'], ENT_QUOTES) ?>?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="path" value="<?= htmlspecialchars($f['path'], ENT_QUOTES) ?>">
                            <input type="hidden" name="domain" value="<?= htmlspecialchars($domain, ENT_QUOTES) ?>">
                            <button type="submit" class="btn-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>
<?PHP endif ?>