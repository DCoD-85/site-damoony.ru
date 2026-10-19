<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
	<h1 style="margin:0;">Блоки: <code><?= htmlspecialchars( $domain ) ?></code></h1>
	<div><a href="?p=block:new&domain=<?= urlencode($domain) ?>" class="btn-edit" style="margin-right:20px;">+ Создать блок</a><a href="?p=dashboard" class="btn-cancel">← К доменам</a></div>
</div>

<p class="hint">
	Базовые блоки (<code>alliasglobal</code>), на которых строятся все страницы. 
	Филлеры (перекрытия для конкретных аллиасов) настраиваются внутри аллиасов.
</p>

<?php if ( empty( $blocks ) ) : ?>
	<p>Блоки не найдены в <code>alliasglobal/</code>.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Блок</th>
				<th>Файл (<code>block.php</code>)</th>
				<th>Данные (<code>block.data.php</code>)</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $blocks as $b ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $b['name'] ) ?></b></td>
					<td><?= $b['has_php'] ? '✓' : '✗' ?></td>
					<td><?= $b['has_data'] ? '✓' : '✗' ?></td>
					<td>
						<a href="?p=block:edit&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-edit">Ред.</a>
						<a href="?p=allias:fillers&domain=<?= urlencode( $domain ) ?>&allias=www" class="btn-edit">Филлеры www</a>
						<a href="?p=block:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-danger" onclick="return confirm('Удалить блок?')">Удалить</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
