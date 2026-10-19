<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
	<h1 style="margin:0;">Блоки: <code><?= htmlspecialchars( $domain ) ?></code></h1>
	<a href="?p=dashboard" class="btn-cancel">← Назад</a>
</div>

<div class="nav-tabs">
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>">Глобальные</a>
	<?php 
	// Получаем список всех аллиасов, чтобы отрисовать табы
	$cfg = new Config( Paths::config() );
	$alliases = array_values(array_unique((array)$cfg->getKeys(['alliases_real', Paths::real($domain)], [])));
	foreach ( $alliases as $a ) : 
	?>
		<a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($a) ?>" class="<?= $a === $allias ? 'active' : '' ?>">Filler: <?= htmlspecialchars($a) ?></a>
	<?php endforeach ?>
</div>

<table class="table">
	<thead>
		<tr>
			<th>Блок</th>
			<th>База</th>
			<th>Filler (<?= htmlspecialchars($allias) ?>)</th>
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
					<div class="td-actions">
						<?php if ( ! $r['has_base'] ) : ?>
							<span class="btn-cancel" style="opacity:0.5; cursor:not-allowed;">Нет базы</span>
						<?php elseif ( ! $r['has_filler'] ) : ?>
							<a href="?p=filler:new&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-edit">+ Создать</a>
						<?php else : ?>
							<a href="?p=filler:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-edit">Ред.</a>
							
						<?php endif ?>
					</div>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
