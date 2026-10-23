<?PHP ?>
<div class="login-box">
	<h1>Вход в админку</h1>

	<?PHP if ( ! empty( $error ) ) : ?>
		<div class="error-msg"><?= htmlspecialchars( $error ) ?></div>
	<?PHP endif ?>

	<form method="post" action="?p=login">
		<?= Csrf::field( ) ?>
		
		<input type="hidden" name="fingerprint" value="">
		
		<label>Логин
			<input type="text" name="login" autofocus autocomplete="username" required>
		</label>
		
		<label>Пароль
			<input type="password" name="password" autocomplete="current-password" required>
		</label>
		
		<button type="submit">Войти</button>
	</form>
</div>
<script src="assets/fingerprint.js"></script>