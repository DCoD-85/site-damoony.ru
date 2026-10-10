<h1>Создать аллиас</h1>

<form method="post" action="?p=allias:new" class="form">
	<label>Имя аллиаса
		<input type="text" name="name" placeholder="oxydog" required pattern="[a-z][a-z0-9_]*">
	</label>
	<label>Домен
		<select name="domain" required>
			<?php foreach ( $domains as $d ) : ?>
				<option value="<?= htmlspecialchars( $d ) ?>"><?= htmlspecialchars( $d ) ?></option>
			<?php endforeach ?>
		</select>
	</label>
	<label class="checkbox">
		<input type="checkbox" name="register" value="1" checked>
		Зарегистрировать в site.config
	</label>
	<button type="submit">Создать</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>