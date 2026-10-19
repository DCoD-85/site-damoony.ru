<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
	<h1 style="margin:0;">Блоки: <code><?= htmlspecialchars( $domain ) ?></code></h1>
	<div class="td-actions">
		<a href="?p=schemes:list&domain=<?= urlencode($domain) ?>" class="btn-edit">Схемы</a>
		<a href="?p=block:new&domain=<?= urlencode($domain) ?>" class="btn-edit">+ Блок</a>
		<a href="?p=dashboard" class="btn-cancel">← Назад</a>
	</div>
</div>

<div class="nav-tabs">
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="active">Глобальные</a>
	<?php foreach ( $alliases as $a ) : ?>
		<a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($a) ?>">Filler: <?= htmlspecialchars($a) ?></a>
	<?php endforeach ?>
</div>

<?php if ( empty( $blocks ) ) : ?>
	<p>Блоки не найдены.</p>
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
						<div class="td-actions">
							<a href="?p=block:edit&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-edit">Ред.</a>
							<a href="?p=block:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-danger" onclick="return confirm('Удалить блок?')">Удалить</a>
						</div>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
