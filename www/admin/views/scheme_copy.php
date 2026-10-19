<h1>Копировать схему: <code><?= htmlspecialchars( $source ) ?></code></h1>
<form method="post" action="?p=scheme:copy&domain=<?= urlencode($domain) ?>&scheme=<?= urlencode($source) ?>" class="form">
    <?= Csrf::field() ?>
    <label>Имя новой схемы (латиница)
        <input type="text" name="target" required pattern="[a-z][a-z0-9_]*" placeholder="new_scheme" autofocus>
    </label>
    <button type="submit">Скопировать</button>
    <a href="?p=schemes:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">Отмена</a>
</form>
