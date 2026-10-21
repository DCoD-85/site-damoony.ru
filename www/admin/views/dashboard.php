<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
	<h1 style="margin:0;">Домены</h1>
	<a href="?p=domain:new" class="btn-edit" style="font-size:15px;">+ Добавить домен</a>
</div>

<?php if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Домен</th>
				<th>Namespace</th>
				<th>Статус</th>
				<th>Файлы</th>
				<th>Аллиасы</th>
				<th>Управление</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $domains as $d ) : ?>
				<?php $d_enc = urlencode( $d[ 'domain' ] ); ?>
				<tr>
					<td>
						<a href="?p=domain:edit&domain=<?= $d_enc ?>" class="domain-link">
							<b><?= htmlspecialchars( $d[ 'domain' ] ) ?></b>
						</a>
						<?php if ( ! empty( $d[ 'aliases' ] ) ) : ?>
							<div class="hint" style="font-size:11px; color:#888; margin-top:4px;">aliases: <?= htmlspecialchars( implode( ', ' , $d[ 'aliases' ] ) ) ?></div>
						<?php endif ?>
					</td>
					<td><code><?= htmlspecialchars( $d[ 'ns' ] ) ?></code></td>
					<td>
						<form method="post" action="?p=domain:toggle" class="inline">
							<?= Csrf::field( ) ?>
							<input type="hidden" name="domain" value="<?= htmlspecialchars( $d[ 'domain' ] , ENT_QUOTES ) ?>">
							<input type="hidden" name="value"  value="<?= $d[ 'avail' ] ? '0' : '1' ?>">
							<button type="submit"
								class="btn-toggle <?= $d[ 'avail' ] ? 'btn-toggle-on' : 'btn-toggle-off' ?>"
								title="<?= $d[ 'avail' ] ? 'Выключить' : 'Включить' ?>">
								<?= $d[ 'avail' ] ? '● вкл' : '○ выкл' ?>
							</button>
						</form>
					</td>
					<td><?= $d[ 'fs' ] ? '✓' : '✗' ?></td>
					<td>
						<div class="allias-list">
							<?php foreach ( $d[ 'alliases' ] as $a ) : ?>
								<a href="?p=allias:fillers&domain=<?= $d_enc ?>&allias=<?= urlencode( $a ) ?>"
									class="allias-link" title="Управление филлерами"><?= htmlspecialchars( $a ) ?></a>
							<?php endforeach ?>
							<a href="?p=allias:new&domain=<?= $d_enc ?>" class="allias-add" title="Новый аллиас">+ новый</a>
						</div>
					</td>
					<td>
						<div class="action-links">
							<a href="?p=allias:list&domain=<?= $d_enc ?>" class="btn-sub">Все аллиасы</a>
							<a href="?p=blocks:list&domain=<?= $d_enc ?>" class="btn-sub">Блоки</a>
							<a href="?p=schemes:list&domain=<?= $d_enc ?>" class="btn-sub">Схемы</a>
							<a href="?p=stub:edit&domain=<?= $d_enc ?>" class="btn-edit">Заглушка</a>
							<a href="?p=domain:remove&domain=<?= $d_enc ?>" class="btn-danger">Удалить</a>
						</div>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
