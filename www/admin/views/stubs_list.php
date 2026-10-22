<?PHP if ( ! empty( $_GET[ 'saved' ] ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<?PHP if ( empty( $stubs ) ) : ?>
	<p>Библиотека заглушек пуста.</p>
<?PHP else : ?>
	<form method="post" action="?p=stubs:edit">
		<?= Csrf::field() ?>
		
		<table class="table">
			<thead>
				<tr>
					<th style="width: 150px;">ID (системный)</th>
					<th style="width: 250px;">Название</th>
					<th>HTML / Текст</th>
				</tr>
			</thead>
			<tbody>
				<?PHP foreach ( $stubs as $id => $s ) : ?>
					<tr>
						<td><code><?= htmlspecialchars( $id ) ?></code></td>
						<td>
							<input type="text" name="stubs[<?= htmlspecialchars( $id ) ?>][title]" value="<?= htmlspecialchars( $s['title'] ?? '' ) ?>" style="width: 100%; padding: 6px; border: 1px solid var(--border); border-radius: 4px;">
						</td>
						<td>
							<textarea name="stubs[<?= htmlspecialchars( $id ) ?>][html]" class="code-editor" style="width:100%; height:100px; font-family:monospace; font-size:12px;"><?= htmlspecialchars( $s['html'] ?? $s['text'] ?? $s['content'] ?? '' ) ?></textarea>
						</td>
					</tr>
				<?PHP endforeach ?>
			</tbody>
		</table>
		
		<div style="margin-top: 20px;">
			<button type="submit" class="btn-primary">Сохранить изменения</button>
		</div>
	</form>
<?PHP endif ?>