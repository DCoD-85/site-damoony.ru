<?PHP if ( empty( $domains ) ) : ?>
	<p>Доменов нет.</p>
<?PHP else : ?>
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
			<?PHP foreach ( $domains as $d ) : ?>
				<?PHP $d_enc = urlencode( $d[ 'domain' ] ); ?>
				<tr style="cursor:pointer;" onclick="window.location='?p=domain:edit&domain=<?= $d_enc ?>';">
					<td>
						<a href="?p=domain:edit&domain=<?= $d_enc ?>" class="domain-link" style="font-size:16px;">
							<b><?= htmlspecialchars( $d[ 'domain' ] ) ?></b>
						</a>
						<?PHP if ( ! empty( $d[ 'aliases' ] ) ) : ?>
							<div class="hint" style="font-size:11px; color:#888; margin-top:4px;">aliases: <?= htmlspecialchars( implode( ', ' , $d[ 'aliases' ] ) ) ?></div>
						<?PHP endif ?>
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
							<?PHP 
							$aliases_to_show = array_slice( $d[ 'alliases' ] , 0 , 3 );
							$hidden_count = count( $d[ 'alliases' ] ) - count( $aliases_to_show );
							?>
							<?PHP foreach ( $aliases_to_show as $a ) : ?>
								<?PHP 
								$a_name = is_array( $a ) ? $a[ 'name' ] : $a;
								$a_avail = is_array( $a ) ? $a[ 'avail' ] : true;
								?>
								<a href="?p=allias:edit&domain=<?= $d_enc ?>&allias=<?= urlencode( $a_name ) ?>" 
								   class="allias-link" onclick="event.stopPropagation();" title="Настройки аллиаса"
								   style="<?= $a_avail ? 'background:#ecfdf5; border-color:#a7f3d0; color:#065f46;' : 'opacity:0.6;' ?>">
									<?= htmlspecialchars( $a_name ) ?>
								</a>
							<?PHP endforeach ?>
							
							<?PHP if ( $hidden_count > 0 ) : ?>
								<?PHP 
								$hidden_names = [];
								foreach ( array_slice( $d['alliases'] , 3 ) as $h ) {
									$hidden_names[] = is_array( $h ) ? $h[ 'name' ] : $h;
								}
								?>
								<span class="allias-link" style="opacity:0.7; cursor:help; background:var(--surface); border-color:var(--border); color:var(--text-dim);" title="<?= htmlspecialchars( implode( ', ' , $hidden_names ) ) ?>">+<?= $hidden_count ?></span>
							<?PHP endif ?>
							
							<a href="?p=allias:new&domain=<?= $d_enc ?>" class="allias-link" onclick="event.stopPropagation();" style="background:var(--surface); border-style:dashed; color:var(--text-dim); font-weight:bold;" title="Добавить аллиас">+</a>
						</div>
					</td>
					<td>
						<a href="?p=domain:remove&domain=<?= $d_enc ?>" class="btn-danger" onclick="event.stopPropagation(); return confirm('Удалить домен <?= $d_enc ?>?');">Удалить</a>
					</td>
				</tr>
			<?PHP endforeach ?>
		</tbody>
	</table>
<?PHP endif ?>