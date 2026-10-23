<?PHP ?>
<p class="hint">Глобальные параметры установки. Применяются ко всем доменам.</p>

<?PHP if ( ! empty( $saved ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<form method="post" action="?p=settings" class="form">
	<?= Csrf::field() ?>

	<label>Движок (logic)
		<select name="logic">
			<?PHP if ( $current[ 'logic' ] === '' ) : ?>
				<option value="">— выберите —</option>
			<?PHP endif ?>
			<?PHP foreach ( $logics as $l ) : ?>
				<option value="<?= htmlspecialchars( $l , ENT_QUOTES ) ?>"
					<?= $l === $current[ 'logic' ] ? 'selected' : '' ?>>
					<?= htmlspecialchars( $l ) ?>
				</option>
			<?PHP endforeach ?>
		</select>
	</label>

	<label>Тип подключения
		<select name="connect_type">
			<?PHP foreach ( $connectTypes as $ct ) : ?>
				<option value="<?= htmlspecialchars( $ct , ENT_QUOTES ) ?>"
					<?= $ct === $current[ 'connect_type' ] ? 'selected' : '' ?>>
					<?= htmlspecialchars( $ct ) ?>
				</option>
			<?PHP endforeach ?>
		</select>
	</label>

	<label class="checkbox">
		<input type="checkbox" name="ui_ajax_forms" value="1" <?= $current['ui_ajax_forms'] ? 'checked' : '' ?>>
		Включить AJAX для форм (ui_ajax_forms)
	</label>

	<div style="display:flex; gap:10px; align-items:center; margin-top:20px;">
		<button type="submit" class="btn-primary">Сохранить</button>
		<a href="?p=dashboard" class="btn-cancel">Отмена</a>
	</div>
</form>