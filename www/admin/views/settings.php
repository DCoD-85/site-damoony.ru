<h1>Настройки CMS</h1>

<p class="hint">Глобальные параметры установки. Применяются ко всем доменам.</p>

<?php if ( ! empty( $saved ) ) : ?>
	<div class="ok-msg">✓ Сохранено</div>
<?php endif ?>

<form method="post" action="?p=settings" class="form">
	<?= Csrf::field() ?>

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

    <label>Тип маршрутизации (logic_type)
        <select name="logic_type">
            <option value="router" <?= $current['logic_type'] === 'router' ? 'selected' : '' ?>>router (новый)</option>
            <option value="chain" <?= $current['logic_type'] === 'chain' ? 'selected' : '' ?>>chain (классический)</option>
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

    <label class="checkbox">
        <input type="checkbox" name="ui_ajax_forms" value="1" <?= $current['ui_ajax_forms'] ? 'checked' : '' ?>>
        Включить AJAX для форм (ui_ajax_forms)
    </label>

	<button type="submit">Сохранить</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>
