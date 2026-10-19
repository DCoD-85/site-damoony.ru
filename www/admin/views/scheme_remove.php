<h1>Удалить схему: <code><?= htmlspecialchars( $scheme ) ?></code>?</h1>
<div class="error-msg">
    <strong>Внимание!</strong> Это действие необратимо. Будут удалены файлы схемы.
</div>
<form method="post" action="?p=scheme:remove&domain=<?= urlencode($domain) ?>&scheme=<?= urlencode($scheme) ?>" class="form">
    <?= Csrf::field() ?>
    <label>Введите имя схемы для подтверждения (<code><?= htmlspecialchars( $scheme ) ?></code>):
        <input type="text" name="confirm" autocomplete="off">
    </label>
    <button type="submit" class="btn-danger-solid">Удалить</button>
    <a href="?p=schemes:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">Отмена</a>
</form>
