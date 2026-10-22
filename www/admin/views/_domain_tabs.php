<?PHP
$active = $meta['tabs']['active_domain'] ?? 'main';
?>
<div class="tabs-row top">
	<div class="nav-tabs-domain">
		<a href="?p=domain:edit&domain=<?= urlencode( $domain ) ?>"  class="<?= $active === 'main'    ? 'active' : '' ?>">Настройки</a>
		<a href="?p=allias:list&domain=<?= urlencode( $domain ) ?>"  class="<?= $active === 'allias'  ? 'active' : '' ?>">Аллиасы</a>
		<a href="?p=blocks:list&domain=<?= urlencode( $domain ) ?>"  class="<?= $active === 'blocks'  ? 'active' : '' ?>">Блоки</a>
		<a href="?p=schemes:list&domain=<?= urlencode( $domain ) ?>" class="<?= $active === 'schemes' ? 'active' : '' ?>">Схемы</a>
		<a href="?p=media:list&domain=<?= urlencode( $domain ) ?>"   class="<?= $active === 'media'   ? 'active' : '' ?>">Медиа</a>
	</div>
</div>
