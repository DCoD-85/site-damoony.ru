<?PHP /* @var string $view_file */ ?>
<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="/admin/">
	<title>Admin — SimplCMS</title>
	<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">

<header class="admin-header">
	<div class="admin-logo">
		<span style="color:#8ab4ff; font-weight:800; font-size:18px; letter-spacing:1px;">SimplCMS</span>
		<span style="color:#8892b0; font-size:12px; margin-left:5px;">/ admin</span>
	</div>
	
	<?php if ( ! empty( $_SESSION[ 'admin_ok' ] ) ) : ?>
		<nav class="admin-nav" style="margin-left: 30px;">
			<a href="?p=dashboard" class="<?= $p === 'dashboard' ? 'active' : '' ?>">Домены</a>
			<a href="?p=stubs:list" class="<?= str_starts_with( $p , 'stubs:' ) ? 'active' : '' ?>">Заглушки</a>
			<a href="?p=settings" class="<?= $p === 'settings' ? 'active' : '' ?>">Настройки</a>
		</nav>
		
		<div class="admin-user">
			<span style="color:#8892b0; margin-right:15px; font-size:13px;">
				👤 <?= htmlspecialchars( $_SESSION[ 'admin_login' ] ?? '' ) ?>
			</span>
			<a href="?p=logout" class="btn-cancel" style="padding:5px 12px; font-size:12px;">Выйти</a>
		</div>
	<?php endif ?>
</header>

<main class="admin-main">
	<?php include $view_file; ?>
</main>

</body>
</html>
