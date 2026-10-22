<?PHP 
// Внутренние табы для выбора аллиаса (Глобальные / Filler: www)
$cfg = new Config( Paths::config() );
$alliases = array_values(array_unique((array)$cfg->getKeys(['alliases_real', Paths::real($domain)], [])));
?>
<div class="nav-tabs" style="margin-bottom:20px;">
	<a href="?p=blocks:list&domain=<?= urlencode($domain) ?>">Глобальные</a>
	<?PHP foreach ( $alliases as $a ) : ?>
		<a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($a) ?>" class="<?= $a === $allias ? 'active' : '' ?>">Filler: <?= htmlspecialchars($a) ?></a>
	<?PHP endforeach ?>
</div>

<table class="table">
	<thead>
		<tr>
			<th>Блок</th>
			<th style="text-align:center;">База</th>
			<th style="text-align:center;">Filler (<?= htmlspecialchars($allias) ?>)</th>
			<th style="text-align:center;">Статус</th>
			<th>Действия</th>
		</tr>
	</thead>
	<tbody>
		<?PHP foreach ( $rows as $r ) : ?>
			<tr>
				<td><b><?= htmlspecialchars($r['name']) ?></b></td>
				<td style="text-align:center;"><?= $r['has_base'] ? '✓' : '✗' ?></td>
				<td style="text-align:center;"><?= $r['has_filler'] ? '✓' : '✗' ?></td>
				<td style="text-align:center;">
					<?PHP if ( $r['has_filler'] ) : ?>
						<form method="post" action="?p=filler:toggle" style="margin:0; display:inline-block;">
							<?= Csrf::field() ?>
							<input type="hidden" name="domain" value="<?= htmlspecialchars($domain, ENT_QUOTES) ?>">
							<input type="hidden" name="allias" value="<?= htmlspecialchars($allias, ENT_QUOTES) ?>">
							<input type="hidden" name="block" value="<?= htmlspecialchars($r['name'], ENT_QUOTES) ?>">
							<input type="hidden" name="value" value="<?= $r['enabled'] ? '0' : '1' ?>">
							<label class="switch" title="<?= $r['enabled'] ? 'Выключить филлер' : 'Включить филлер' ?>">
								<input type="checkbox" onchange="this.form.submit()" <?= $r['enabled'] ? 'checked' : '' ?>>
								<span class="slider"></span>
							</label>
						</form>
					<?PHP else : ?>—<?PHP endif ?>
				</td>
				<td>
					<div class="td-actions">
						<?PHP if ( ! $r['has_base'] ) : ?>
							<span class="btn-cancel" style="opacity:0.5; cursor:not-allowed;">Нет базы</span>
						<?PHP elseif ( ! $r['has_filler'] ) : ?>
							<a href="?p=filler:new&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-primary">+ Создать</a>
						<?PHP else : ?>
							<a href="?p=filler:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-edit">Ред.</a>
							<a href="?p=filler:remove&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&block=<?= urlencode($r['name']) ?>" class="btn-danger" onclick="return confirm('Удалить филлер?')">Удалить</a>
						<?PHP endif ?>
					</div>
				</td>
			</tr>
		<?PHP endforeach ?>
	</tbody>
</table>