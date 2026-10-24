<form method="post" action="" class="form" style="max-width: 800px;">
    <?= Csrf::field() ?>
    
    <?PHP foreach ($resource['fields'] as $name => $field): ?>
        <?PHP if ($name === 'id' || $name === 'created_at') continue; ?>
        
        <label><?= htmlspecialchars($field['label'] ?? $name) ?>
            <?PHP if ($field['type'] === 'text'): ?>
                <textarea name="<?= $name ?>" rows="5"><?= htmlspecialchars((string)($row[$name] ?? '')) ?></textarea>
            <?PHP elseif ($field['type'] === 'boolean'): ?>
                <input type="checkbox" name="<?= $name ?>" value="1" <?= !empty($row[$name]) ? 'checked' : '' ?>>
            <?PHP elseif ($field['type'] === 'relation'): ?>
                <select name="<?= $name ?>">
                    <option value="">— Выберите —</option>
                    <?PHP foreach ($relations[$name] ?? [] as $opt): ?>
                        <option value="<?= $opt['id'] ?>" <?= (int)($row[$name] ?? 0) === (int)$opt['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($opt['label']) ?>
                        </option>
                    <?PHP endforeach; ?>
                </select>
            <?PHP else: ?>
                <input type="text" name="<?= $name ?>" value="<?= htmlspecialchars((string)($row[$name] ?? '')) ?>">
            <?PHP endif; ?>
        </label>
    <?PHP endforeach; ?>

    <div style="margin-top: 20px;">
        <button type="submit" class="btn-primary">Сохранить</button>
        <a href="?p=resource:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&resource=<?= urlencode($resource_id) ?>" class="btn-cancel">Назад</a>
    </div>
</form>