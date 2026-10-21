<?php
// Проверяем активный таб
$tab = $_GET['tab'] ?? 'main';
?>

<?php $active_tab = 'main'; require __DIR__ . '/_domain_tabs.php'; ?>

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

<form method="post" action="?p=domain:edit&domain=<?= urlencode( $domain ) ?>&tab=main" class="form">
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

    <label class="checkbox" style="margin-top:15px;">
        <input type="checkbox" name="domain_available" value="1" <?= $current[ 'avail' ] ? 'checked' : '' ?>>
        <b>Включить сайт</b> (если снять галочку — сайт покажет заглушку)
    </label>

    <button type="submit" class="btn-edit" style="margin-top:15px;">Сохранить</button>
</form>
