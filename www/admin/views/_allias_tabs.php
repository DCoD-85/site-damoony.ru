<?php 
$active_tab = $active_tab ?? ''; 
$actions = $actions ?? ''; 
?>
<div class="tabs-row bottom">
    <div class="nav-tabs-allias">
        <a href="?p=allias:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="<?= $active_tab === 'main' ? 'active' : '' ?>">Настройки</a>
        <a href="?p=pages:tree&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="<?= $active_tab === 'pages' ? 'active' : '' ?>">Страницы</a>
        <a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="<?= $active_tab === 'fillers' ? 'active' : '' ?>">Филлеры</a>
        <a href="?p=scheme:edit&domain=<?= urlencode($domain) ?>&scheme=<?= urlencode($allias) ?>" class="<?= $active_tab === 'scheme' ? 'active' : '' ?>">Схема</a>
    </div>
    <div style="margin-top: 5px;">
        <?= $actions ?>
    </div>
</div>
