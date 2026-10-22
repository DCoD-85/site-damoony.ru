<?php if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?php else : ?>
	<table class="table">
		<thead>
			<tr>
				<th>Домен</th>
				<th>Namespace</th>
				<th style="text-align:center;">Статус</th>
				<th style="text-align:center;">Файлы</th>
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
					<td style="text-align:center;" onclick="event.stopPropagation();">
						<form method="post" action="?p=domain:toggle" style="margin:0; display:inline-block;">
							<?= Csrf::field() ?>
							<input type="hidden" name="domain" value="<?= htmlspecialchars( $d[ 'domain' ] , ENT_QUOTES ) ?>">
							<input type="hidden" name="value"  value="<?= $d[ 'avail' ] ? '0' : '1' ?>">
							<label class="switch" title="<?= $d[ 'avail' ] ? 'Выключить сайт' : 'Включить сайт' ?>">
								<input type="checkbox" onchange="this.form.submit()" <?= $d[ 'avail' ] ? 'checked' : '' ?>>
								<span class="slider"></span>
							</label>
						</form>
					</td>
					<td style="text-align:center;"><?= $d[ 'fs' ] ? '✓' : '✗' ?></td>
					<td>
						<div class="allias-list">
							<?php foreach ( $d[ 'alliases' ] as $a ) : ?>
								<?php 
								// Поддержка старого и нового формата
								$a_name = is_array( $a ) ? $a[ 'name' ] : $a;
								$a_avail = is_array( $a ) ? $a[ 'avail' ] : true;
								?>
								<a href="?p=allias:edit&domain=<?= $d_enc ?>&allias=<?= urlencode( $a_name ) ?>" 
								   class="allias-link" onclick="event.stopPropagation();" title="Настройки аллиаса"
								   style="<?= $a_avail ? 'background:#e3f7e8; border-color:#c6e6d0; color:#1a7a3b;' : 'opacity:0.6;' ?>">
									<?= htmlspecialchars( $a_name ) ?>
								</a>
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