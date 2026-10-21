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
				<th>Действия</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $domains as $d ) : ?>
				<?php $d_enc = urlencode( $d[ 'domain' ] ); ?>
				<tr style="cursor:pointer;" onclick="window.location='?p=domain:edit&domain=<?= $d_enc ?>';">
					<td>
						<a href="?p=domain:edit&domain=<?= $d_enc ?>" class="domain-link" style="font-size:16px;">
							<b><?= htmlspecialchars( $d[ 'domain' ] ) ?></b>
						</a>
						<?php if ( ! empty( $d[ 'aliases' ] ) ) : ?>
							<div class="hint" style="font-size:11px; color:#888; margin-top:4px;">aliases: <?= htmlspecialchars( implode( ', ' , $d[ 'aliases' ] ) ) ?></div>
						<?php endif ?>
					</td>
					<td><code><?= htmlspecialchars( $d[ 'ns' ] ) ?></code></td>
					<td>
						<?= $d[ 'avail' ] ? '<span style="color:#1a7a3b; font-weight:bold;">● вкл</span>' : '<span style="color:#a11; font-weight:bold;">○ выкл</span>' ?>
					</td>
					<td><?= $d[ 'fs' ] ? '✓' : '✗' ?></td>
					<td>
						<div class="allias-list">
							<?php foreach ( $d[ 'alliases' ] as $a ) : ?>
								<span class="allias-link" style="cursor:default;"><?= htmlspecialchars( $a ) ?></span>
							<?php endforeach ?>
						</div>
					</td>
					<td>
						<a href="?p=domain:remove&domain=<?= $d_enc ?>" class="btn-danger" onclick="event.stopPropagation(); return confirm('Удалить домен <?= $d_enc ?>?');">Удалить</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
