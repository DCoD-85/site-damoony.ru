<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
	<h1 style="margin:0;">Fillers: <code><?= htmlspecialchars($domain) ?></code> / <code><?= htmlspecialchars($allias) ?></code></h1>
	<a href="?p=allias:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">← Назад к аллиасам</a>
</div>

<table class="table">
	<thead>
		<tr>
			<th>Блок</th>
			<th>База (<code>block.php</code>)</th>
			<th>Filler (<code>block_<?= htmlspecialchars($allias) ?>.php</code>)</th>
			<th>Статус</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $rows as $r ) : ?>
			<tr>
				<td><b><?= htmlspecialchars($r['name']) ?></b></td>
				<td><?= $r['has_base'] ? '✓' : '✗' ?></td>
				<td><?= $r['has_filler'] ? '✓' : '✗' ?></td>
				<td>
					<?php if ( $r['has_filler'] ) : ?>
						<form method="post" action="?p=filler:toggle" class="inline">
							<?= Csrf::field() ?>
							<input type="hidden" name="domain" value="<?= htmlspecialchars($domain, ENT_QUOTES) ?>">
							<input type="hidden" name="allias" value="<?= htmlspecialchars($allias, ENT_QUOTES) ?>">
							<input type="hidden" name="block"  value="<?= htmlspecialchars($r['name'], ENT_QUOTES) ?>">
							<input type="hidden" name="value"  value="<?= $r['enabled'] ? '0' : '1' ?>">
							<button type="submit" class="btn-toggle <?= $r['enabled'] ? 'btn-toggle-on' : 'btn-toggle-off' ?>">
								<?= $r['enabled'] ? '● вкл' : '○ выкл' ?>
							</button>
						</form>
					<?php else : ?>
						—
					<?php endif ?>
				</td>
				<td>
					<?php if ( ! $r['has_base'] ) : ?>
						<span class="btn-danger">Нет базы</span>
					<?php elseif ( ! $r['has_filler'] ) : ?>
						<a href="?p=filler:new&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-edit">+ Создать</a>
					<?php else : ?>
						<a href="?p=filler:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-edit">Ред.</a>
						<a href="?p=filler:remove&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-danger" onclick="return confirm('Удалить филлер?')">Удалить</a>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
