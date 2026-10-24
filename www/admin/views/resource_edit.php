<form method="post" action="" class="form" style="max-width: 800px;">
    <?= Csrf::field() ?>
    
    <?PHP foreach ($resource['fields'] as $name => $field): ?>
        <?PHP if ($name === 'id' || $name === 'created_at') continue; ?>
        
        <label><?= htmlspecialchars($field['label'] ?? $name) ?>
            <?PHP if ($field['type'] === 'text'): ?>
                <textarea name="<?= $name ?>" rows="5"><?= htmlspecialchars($data[$name] ?? '') ?></textarea>
            <?PHP elseif ($field['type'] === 'boolean'): ?>
                <input type="checkbox" name="<?= $name ?>" value="1" <?= !empty($data[$name]) ? 'checked' : '' ?>>
            <?PHP else: ?>
                <input type="text" name="<?= $name ?>" value="<?= htmlspecialchars($data[$name] ?? '') ?>">
            <?PHP endif; ?>
        </label>
    <?PHP endforeach; ?>

    <div style="margin-top: 20px;">
        <button type="submit" class="btn-primary">Сохранить</button>
        <a href="?p=resources&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" class="btn-cancel">Назад</a>
    </div>
</form>