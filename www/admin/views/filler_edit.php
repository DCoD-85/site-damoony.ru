<?php 
$parent_link = '?p=allias:fillers&domain=' . urlencode($domain) . '&allias=' . urlencode($allias);
$parent_title = 'Филлеры';
$current_title = $block . ' (' . $allias . ')';
require __DIR__ . '/_context_nav.php'; 
?><?php endif ?>
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
