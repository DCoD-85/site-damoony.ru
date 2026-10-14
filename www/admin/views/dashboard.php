<h1>Домены</h1>

<?php if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Домен</th>
				<th>Namespace</th>
				<th>Домен</th>
				<th>Файлы</th>
				<th>Аллиасы</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $domains as $d ) : ?>
				<tr>
					<td>
						<b><?= htmlspecialchars( $d[ 'domain' ] ) ?></b>
						<?php if ( ! empty( $d[ 'aliases' ] ) ) : ?>
							<div class="hint" style="font-size:11px;color:#888;">
								aliases: <?= htmlspecialchars( implode( ', ' , $d[ 'aliases' ] ) ) ?>
							</div>
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
						<a href="?p=domain:remove&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
							class="btn-danger">Удалить</a>
					</td>
					<td><?= $d[ 'fs' ]    ? '✓' : '✗' ?></td>
					<td><?= htmlspecialchars( implode( ', ' , $d[ 'alliases' ] ) ) ?></td>
					<td>
						<a href="?p=domain:edit&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
							class="btn-edit">Редактировать</a>
						<a href="?p=allias:list&domain=<?= urlencode( $d[ 'domain' ] ) ?>"
							class="btn-edit">Аллиасы</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>