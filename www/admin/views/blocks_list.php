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

<h3>Блоки данных (alliasglobal)</h3>
<?php if ( empty( $blocks ) ) : ?>
	<p>Блоки не найдены.</p>
<?php else : ?>
	<table class="table" style="margin-bottom:30px;">
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
							<?php if ( $b['has_php'] ) : ?>
								<a href="?p=block:edit&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-edit">Ред.</a>
								<a href="?p=block:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-danger" onclick="return confirm('Удалить блок?')">Удалить</a>
							<?php else : ?>
								<a href="?p=block:new&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-edit" style="background:#e3f7e8; border-color:#c6e6d0; color:#1a7a3b;">Восстановить</a>
							<?php endif ?>
						</div>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>

<h3>Визуальные контейнеры (Layout)</h3>
<p class="hint" style="font-size:12px; color:#666; margin-top:-10px; margin-bottom:10px;">
	Эти элементы используются только в схемах для сетки (обертки). У них нет файлов блоков и данных.
</p>
<?php if ( empty( $layouts ) ) : ?>
	<p>Визуальные контейнеры не найдены.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Класс в схеме</th>
				<th>Тип</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $layouts as $l ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $l ) ?></b></td>
					<td><span style="background:#eef1f5; padding:2px 6px; border-radius:3px; font-size:11px;">layout</span></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
