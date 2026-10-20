<h1>Редактирование: <code><?= htmlspecialchars($name) ?></code></h1>
<?php if ( !empty($_GET['saved']) ) : ?><div class="ok-msg">✓ Сохранено</div><?php endif ?>
<form method="post" class="form" style="max-width:100%;">
	<?= Csrf::field() ?>
	<label>PHP (<code><?= $name ?>.php</code>)
		<textarea name="php" rows="15"><?= htmlspecialchars($php) ?></textarea>
	</label>
	<label>Данные (<code><?= $name ?>.data.php</code>)
		<textarea name="data" rows="10"><?= htmlspecialchars($file_data) ?></textarea>
	</label>
	<button type="submit">Сохранить</button>
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">Назад</a>
</form>
