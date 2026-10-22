<?PHP ?>
<h3>Блоки данных (alliasglobal)</h3>
<?PHP if ( empty( $blocks ) ) : ?>
	<p>Блоки не найдены.</p>
<?PHP else : ?>
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
			<?PHP foreach ( $blocks as $b ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $b['name'] ) ?></b></td>
					<td><?= $b['has_php'] ? '✓' : '✗' ?></td>
					<td><?= $b['has_data'] ? '✓' : '✗' ?></td>
					<td>
						<div class="td-actions">
							<?PHP if ( $b['has_php'] ) : ?>
								<a href="?p=block:edit&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-edit">Ред.</a>
								<a href="?p=block:remove&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-danger" onclick="return confirm('Удалить блок?')">Удалить</a>
							<?PHP else : ?>
								<a href="?p=block:new&domain=<?= urlencode( $domain ) ?>&name=<?= urlencode( $b['name'] ) ?>" class="btn-primary">Восстановить</a>
							<?PHP endif ?>
						</div>
					</td>
				</tr>
			<?PHP endforeach ?>
		</tbody>
	</table>
<?PHP endif ?>

<h3>Визуальные контейнеры (Layout)</h3>
<p class="hint">
	Эти элементы используются только в схемах для сетки (обертки). У них нет файлов блоков и данных.
</p>
<?PHP if ( empty( $layouts ) ) : ?>
	<p>Визуальные контейнеры не найдены.</p>
<?PHP else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Класс в схеме</th>
				<th>Тип</th>
			</tr>
		</thead>
		<tbody>
			<?PHP foreach ( $layouts as $l ) : ?>
				<tr>
					<td><b><?= htmlspecialchars( $l ) ?></b></td>
					<td><span class="badge">layout</span></td>
				</tr>
			<?PHP endforeach ?>
		</tbody>
	</table>
<?PHP endif ?>