<?PHP /* @var string $view_file */ ?>
<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin — SimplCMS</title>
	<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
	<div class="admin-logo">SimplCMS / admin</div>
	<?php if ( ! empty( $_SESSION[ 'admin_ok' ] ) ) : ?>
		<nav class="admin-nav">
			<a href="?p=settings">Настройки</a>
		</nav>
		<div class="admin-user">
			<?= htmlspecialchars( $_SESSION[ 'admin_login' ] ?? '' ) ?>
			<a href="?p=logout">Выйти</a>
		</div>
	<?php endif ?>
</header>

<main class="admin-main">
	<?php include $view_file; ?>
</main>

</body>
</html>