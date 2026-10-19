<h1>Новый глобальный блок</h1>
<form method="post" action="?p=block:new&domain=<?= urlencode($domain) ?>" class="form">
	<?= Csrf::field() ?>
	<input type="hidden" name="domain" value="<?= htmlspecialchars($domain, ENT_QUOTES) ?>">
	<label>Имя блока (латиница)
		<input type="text" name="name" required pattern="[a-z][a-z0-9_]*" placeholder="sidebar" autofocus>
	</label>
	<button type="submit">Создать</button>
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">Отмена</a>
</form>
