<?php 
$active_tab = $active_tab ?? ''; 
$actions = $actions ?? ''; 
?>
<div class="tabs-row top">
    <div class="nav-tabs-domain">
        <a href="?p=domain:edit&domain=<?= urlencode($domain) ?>" class="<?= $active_tab === 'main' ? 'active' : '' ?>">Настройки</a>
        <a href="?p=allias:list&domain=<?= urlencode($domain) ?>" class="<?= $active_tab === 'allias' ? 'active' : '' ?>">Аллиасы</a>
        <a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="<?= $active_tab === 'blocks' ? 'active' : '' ?>">Блоки</a>
        <a href="?p=schemes:list&domain=<?= urlencode($domain) ?>" class="<?= $active_tab === 'schemes' ? 'active' : '' ?>">Схемы</a>
        <a href="?p=media:list&domain=<?= urlencode($domain) ?>" class="<?= $active_tab === 'media' ? 'active' : '' ?>">Медиа</a>
    </div>
    <div style="margin-bottom: 5px;">
        <?= $actions ?>
    </div>
</div>
