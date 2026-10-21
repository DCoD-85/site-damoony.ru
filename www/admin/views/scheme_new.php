<?php
$active_tab = 'schemes';
$actions = '';
require __DIR__ . '/_domain_tabs.php';
?>

<?php if ( ! empty( $errors ) ) : ?>
    <div class="error-msg">
        <?php foreach ( $errors as $e ) : ?>
            <div>✘ <?= htmlspecialchars( $e ) ?></div>
        <?php endforeach ?>
    </div>
<?php endif ?>

<form method="post" class="form" style="max-width:500px;">
    <?= Csrf::field() ?>
    
    <label>Имя новой схемы (латиница)
        <input type="text" name="target" required pattern="[a-z][a-z0-9_]*" placeholder="new_scheme" autofocus>
    </label>
    
    <label>Базовая схема (откуда скопировать)
        <select name="source">
            <?php foreach ( $schemes as $s ) : ?>
                <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
            <?php endforeach ?>
        </select>
    </label>
    
    <button type="submit" class="btn-edit">Создать схему</button>
</form>
