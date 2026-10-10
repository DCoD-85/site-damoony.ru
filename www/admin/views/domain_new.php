<h1>Создать домен</h1>

<form method="post" action="?p=domain:new" class="form">
	<label>Домен
		<input type="text" name="domain" placeholder="example.com" required pattern="[a-z0-9.\-]+">
	</label>
	<label class="checkbox">
		<input type="checkbox" name="register" value="1" checked>
		Зарегистрировать в site.config
	</label>
	<button type="submit">Создать</button>
	<a href="?p=dashboard" class="btn-cancel">Отмена</a>
</form>