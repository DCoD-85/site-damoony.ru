<?PHP
$active = $meta['tabs']['active_allias'] ?? 'main';
?>
<div class="tabs-row bottom">
	<div class="nav-tabs-allias">
		<a href="?p=allias:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>"    class="<?= $active === 'main'    ? 'active' : '' ?>">Настройки</a>
		<a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>"     class="<?= $active === 'pages'   ? 'active' : '' ?>">Страницы</a>
		<a href="?p=allias:fillers&domain=<?= urlencode( $domain ) ?>&allias=<?= urlencode( $allias ) ?>" class="<?= $active === 'fillers' ? 'active' : '' ?>">Филлеры</a>
		<a href="?p=scheme:edit&domain=<?= urlencode( $domain ) ?>&scheme=<?= urlencode( $allias ) ?>"    class="<?= $active === 'scheme'  ? 'active' : '' ?>">Схема</a>
	</div>
</div>
