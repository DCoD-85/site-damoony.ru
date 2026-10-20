<h1>Редактирование филлера: <code><?= htmlspecialchars($allias) ?>/<?= htmlspecialchars($block) ?></code></h1>
<?php if ( !empty($_GET['saved']) ) : ?><div class="ok-msg">✓ Сохранено</div><?php endif ?>
<form method="post" class="form" style="max-width:100%;">
	<?= Csrf::field() ?>
	<label>PHP (<code><?= $block ?>_<?= $allias ?>.php</code>)
		<textarea name="php" rows="12"><?= htmlspecialchars($php) ?></textarea>
	</label>
	<label>Данные (<code><?= $block ?>_<?= $allias ?>.data.php</code>)
		<textarea name="data" rows="15"><?= htmlspecialchars($file_data) ?></textarea>
	</label>
	<button type="submit">Сохранить</button>
	<a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="btn-cancel">Назад</a>
</form>
