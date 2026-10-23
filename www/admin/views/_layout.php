<?PHP /* @var string $view_file */
$p = $_GET['p'] ?? 'dashboard';

$title       = $meta['title']       ?? null;
$actions     = $meta['actions']     ?? '';
$breadcrumbs = $meta['breadcrumbs'] ?? [];
$tabs        = $meta['tabs']        ?? [];
$showTabs    = ! empty( $tabs['show'] );
?>
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
		<nav class="admin-nav" style="margin-left: 20px;">
			<a href="?p=dashboard"  class="<?= $p === 'dashboard' ? 'active' : '' ?>">Домены</a>
			<a href="?p=stubs:list" class="<?= str_starts_with( $p , 'stub' ) ? 'active' : '' ?>">Заглушки</a>
			<a href="?p=security"   class="<?= str_starts_with( $p , 'security' ) ? 'active' : '' ?>">Безопасность</a> <!-- ДОБАВИЛИ СТРОКУ -->
			<a href="?p=settings"   class="<?= $p === 'settings'  ? 'active' : '' ?>">Настройки</a>
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
	<?php if ( $title !== null || ! empty( $breadcrumbs ) || $actions !== '' ) : ?>
	<div class="page-header">
		<div class="ph-left">
			<?php if ( ! empty( $breadcrumbs ) ) : ?>
				<nav class="breadcrumbs">
					<?php foreach ( $breadcrumbs as $i => $bc ) : ?>
						<?php if ( ! empty( $bc['url'] ) ) : ?>
							<a href="<?= htmlspecialchars( $bc['url'] , ENT_QUOTES ) ?>"><?= htmlspecialchars( $bc['label'] ) ?></a>
						<?php else : ?>
							<span class="current"><?= htmlspecialchars( $bc['label'] ) ?></span>
						<?php endif ?>
						<?php if ( $i < count( $breadcrumbs ) - 1 ) : ?><span class="sep">/</span><?php endif ?>
					<?php endforeach ?>
				</nav>
			<?php endif ?>
			<?php if ( $title !== null ) : ?>
				<h1 class="page-title"><?= htmlspecialchars( $title ) ?></h1>
			<?php endif ?>
		</div>
		<?php if ( $actions !== '' ) : ?>
			<div class="ph-right"><?= $actions ?></div>
		<?php endif ?>
	</div>
	<?php endif ?>

	<?php if ( $showTabs ) : ?>
		<div class="tabs-wrapper">
			<?php if ( ! empty( $tabs['domain'] ) ) require __DIR__ . '/_domain_tabs.php'; ?>
			<?php if ( ! empty( $tabs['allias'] ) ) require __DIR__ . '/_allias_tabs.php'; ?>
		</div>
	<?php endif ?>

	<?php include $view_file; ?>
</main>

</body>
</html>
