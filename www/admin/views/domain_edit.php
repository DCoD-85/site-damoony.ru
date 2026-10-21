<?php
$active_tab = 'main';
$actions = '';
require __DIR__ . '/_domain_tabs.php';
?>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=domain:edit&domain=<?= urlencode( $domain ) ?>" class="form" style="max-width: 600px;">
    <?= Csrf::field() ?>

    <label>Заголовок (title)
        <input type="text" name="title" maxlength="100" required
            value="<?= htmlspecialchars( $current[ 'title' ] , ENT_QUOTES ) ?>">
    </label>

    <label>Тема (skin)
        <select name="skin">
            <?php foreach ( $skins as $s ) : ?>
                <option value="<?= htmlspecialchars( $s , ENT_QUOTES ) ?>"
                    <?= $s === $current[ 'skin' ] ? 'selected' : '' ?>>
                    <?= htmlspecialchars( $s ) ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <hr style="margin: 20px 0; border-top: 1px dashed #ccc;">
    <h3>Статус сайта</h3>

    <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
        <label class="switch" style="margin:0;">
            <input type="checkbox" name="domain_available" value="1" <?= $current[ 'avail' ] ? 'checked' : '' ?>>
            <span class="slider"></span>
        </label>
        <span>Сайт включен (если выключить — покажется заглушка)</span>
    </div>

    <label>Время действия статуса (авто-включение)
        <input type="datetime-local" name="stub_until_date" value="<?= !empty($current['until']) ? date('Y-m-d\TH:i', $current['until']) : '' ?>">
        <small style="color:#888;">Если указать время, сайт автоматически включится в этот момент (если он выключен).</small>
    </label>

    <button type="submit" class="btn-edit" style="margin-top:15px;">Сохранить</button>
</form>

<div style="margin-top: 40px; padding: 20px; background: #fdf2f2; border: 1px solid #f5c6c6; border-radius: 6px; max-width: 600px;">
    <h3 style="color:#a11; margin-top:0;">Опасная зона</h3>
    <p style="font-size:13px; color:#666;">Удаление домена приведет к удалению всех аллиасов, блоков и настроек.</p>
    <a href="?p=domain:remove&domain=<?= urlencode($domain) ?>" class="btn-danger-solid" onclick="return confirm('Удалить домен?');">Удалить домен</a>
</div>
