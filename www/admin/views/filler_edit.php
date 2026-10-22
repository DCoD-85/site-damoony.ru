<?PHP ?>
<form method="post" class="form" style="max-width:100%;">
    <?= Csrf::field() ?>
    
    <label>PHP (<code><?= htmlspecialchars( $block ) ?>_<?= htmlspecialchars( $allias ) ?>.php</code>)
        <textarea name="php" rows="12" class="code-editor" spellcheck="false"><?= htmlspecialchars( $php ) ?></textarea>
    </label>
    
    <label>Данные (<code><?= htmlspecialchars( $block ) ?>_<?= htmlspecialchars( $allias ) ?>.data.php</code>)
        <textarea name="data" rows="15" class="code-editor" spellcheck="false"><?= htmlspecialchars( $file_data ) ?></textarea>
    </label>
    
    <div style="display:flex; gap:10px; align-items:center; margin-top:15px;">
        <button type="submit" class="btn-primary">Сохранить</button>
        <a href="?p=allias:fillers&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="btn-cancel">Назад</a>
    </div>
</form>