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

    <hr style="margin: 20px 0; border-top: 1px dashed #ccc;">
    <h3>Заглушка (Технические работы)</h3>
    
    <label class="checkbox">
        <input type="checkbox" name="stub_enabled" value="1" <?= !empty($stub["enabled"]) ? "checked" : "" ?>>
        Включить заглушку для всего домена
    </label>

    <label>Заголовок заглушки
        <input type="text" name="stub_title" value="<?= htmlspecialchars($stub["title"] ?? "Технические работы") ?>">
    </label>

    <label>Текст заглушки
        <textarea name="stub_text"><?= htmlspecialchars($stub["text"] ?? "Мы скоро вернемся!") ?></textarea>
    </label>

    <label>Время окончания (timestamp)
        <input type="number" name="stub_until" value="<?= (int)($stub["until"] ?? 0) ?>" placeholder="0 — бессрочно">
    </label>

    <button type="submit">Сохранить</button>
    <a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>
