<h1>Домен: <code><?= htmlspecialchars( $domain ) ?></code></h1>

<?php if ( ! empty( $saved ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<?php if ( ! empty( $errors ) ) : ?>
    <div class="error-msg">
        <?php foreach ( $errors as $e ) : ?>
            <div>✘ <?= htmlspecialchars( $e ) ?></div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<form method="post" action="?p=domain:edit&domain=<?= urlencode( $domain ) ?>" class="form">
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

    <button type="submit" class="btn-edit">Сохранить</button>
    <a href="?p=dashboard" class="btn-cancel">Отмена</a>
    
    <div style="margin-top:30px; padding-top:20px; border-top:1px dashed #ccc;">
        <a href="?p=stub:edit&domain=<?= urlencode($domain) ?>" class="btn-sub" style="font-size:14px;">⚙️ Настроить заглушку домена</a>
    </div>
</form>
