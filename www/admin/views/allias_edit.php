<?php 
$active_tab = 'allias'; $actions = ''; require __DIR__ . '/_domain_tabs.php'; 

$active_tab = 'main'; $actions = ''; require __DIR__ . '/_allias_tabs.php'; 
?>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=allias:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="form" style="max-width: 600px;">
    <?= Csrf::field() ?>

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
        <label class="switch" style="margin:0;">
            <input type="checkbox" name="available" value="1" <?= $avail ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
        <span>Аллиас включен (доступен для посещения)</span>
    </div>

    <button type="submit" class="btn-edit" style="margin-top:15px;">Сохранить</button>
</form>

<div style="margin-top: 40px; padding: 20px; background: #fdf2f2; border: 1px solid #f5c6c6; border-radius: 6px; max-width: 600px;">
    <h3 style="color:#a11; margin-top:0;">Опасная зона</h3>
    <p style="font-size:13px; color:#666;">Удаление аллиаса приведет к удалению всех его страниц и филлеров.</p>
    <a href="?p=allias:remove&domain=<?= urlencode($domain) ?>&name=<?= urlencode($allias) ?>" class="btn-danger-solid" onclick="return confirm('Удалить аллиас <?= htmlspecialchars($allias) ?>?');">Удалить аллиас</a>
</div>
