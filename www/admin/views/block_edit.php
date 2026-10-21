<?php 
$parent_link = '?p=blocks:list&domain=' . urlencode($domain);
$parent_title = 'Блоки';
$current_title = $name;
require __DIR__ . '/_context_nav.php'; 
?><?php endif ?>
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
