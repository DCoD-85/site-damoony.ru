<?PHP ?>
<form method="post" class="form" style="max-width:100%;">
    <?= Csrf::field() ?>
    
    <label>PHP (<code><?= htmlspecialchars( $name ) ?>.php</code>)
        <textarea name="php" rows="15" class="code-editor" spellcheck="false"><?= htmlspecialchars( $php ) ?></textarea>
    </label>
    
    <label>Данные (<code><?= htmlspecialchars( $name ) ?>.data.php</code>)
        <textarea name="data" rows="10" class="code-editor" spellcheck="false"><?= htmlspecialchars( $file_data ) ?></textarea>
    </label>
    
    <div style="display:flex; gap:10px; align-items:center; margin-top:15px;">
        <button type="submit" class="btn-primary">Сохранить</button>
        <a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">Назад</a>
    </div>
</form>