<div class="login-box">
	<h1>Вход в админку</h1>

	<?php if ( ! empty( $error ) ) : ?>
		<div class="error-msg"><?= htmlspecialchars( $error ) ?></div>
	<?php endif ?>

	<form method="post" action="?p=login">
		<?= Csrf::field( ) ?>
		<label>Логин
			<input type="text" name="login" autofocus autocomplete="username">
		</label>
		<label>Пароль
			<input type="password" name="password" autocomplete="current-password">
		</label>
		<button type="submit">Войти</button>
	</form>
</div>