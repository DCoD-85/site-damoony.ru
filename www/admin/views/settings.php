<h1>Настройки</h1>

<p class="hint">Глобальные параметры установки. Применяются ко всем доменам.</p>

<?php if ( ! empty( $saved ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=settings" class="form">

	<label>Движок (logic)
		<select name="logic">
			<?php if ( $current[ 'logic' ] === '' ) : ?>
				<option value="">— выберите —</option>
			<?php endif ?>
			<?php foreach ( $logics as $l ) : ?>
				<option value="<?= htmlspecialchars( $l , ENT_QUOTES ) ?>"
					<?= $l === $current[ 'logic' ] ? 'selected' : '' ?>>
					<?= htmlspecialchars( $l ) ?>
				</option>
			<?php endforeach ?>
		</select>
	</label>

	<label>Тип подключения
		<select name="connect_type">
			<?php foreach ( $connectTypes as $ct ) : ?>
				<option value="<?= htmlspecialchars( $ct , ENT_QUOTES ) ?>"
					<?= $ct === $current[ 'connect_type' ] ? 'selected' : '' ?>>
					<?= htmlspecialchars( $ct ) ?>
				</option>
			<?php endforeach ?>
		</select>
	</label>

	<button type="submit">Сохранить</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>