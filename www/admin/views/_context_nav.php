<?php
/**
 * Универсальная панель навигации и контекста.
 * Ожидает: $domain, $allias (опционально), $parent_link (опц.), $parent_title (опц.), $current_title (опц.)
 */
$domain_enc = urlencode($domain ?? '');
$allias_enc = urlencode($allias ?? '');
?>
<div style="background:#f8f9fa; border:1px solid #e0e4eb; border-radius:6px; padding:10px 15px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:center; font-size:13px;">
    <div>
        <a href="?p=dashboard" style="color:#2a63d6; text-decoration:none;">Домены</a>
        <span style="color:#999; margin:0 5px;">/</span>
        
        <a href="?p=domain:edit&domain=<?= $domain_enc ?>" style="color:#2a63d6; text-decoration:none; font-weight:bold;">
            <?= htmlspecialchars($domain ?? '') ?>
        </a>

        <?php if ( ! empty( $allias ) ) : ?>
            <span style="color:#999; margin:0 5px;">/</span>
            <a href="?p=allias:fillers&domain=<?= $domain_enc ?>&allias=<?= $allias_enc ?>" style="color:#2a63d6; text-decoration:none;">
                <?= htmlspecialchars($allias) ?>
            </a>
        <?php endif ?>

        <?php if ( ! empty( $parent_title ) ) : ?>
            <span style="color:#999; margin:0 5px;">/</span>
            <a href="<?= $parent_link ?? '#' ?>" style="color:#2a63d6; text-decoration:none;">
                <?= htmlspecialchars( $parent_title ) ?>
            </a>
        <?php endif ?>

        <?php if ( ! empty( $current_title ) ) : ?>
            <span style="color:#999; margin:0 5px;">/</span>
            <span style="color:#333; font-weight:bold;">
                <?= htmlspecialchars( $current_title ) ?>
            </span>
        <?php endif ?>
    </div>

    <div>
        <?= $actions ?? '' ?>
    </div>
</div>
