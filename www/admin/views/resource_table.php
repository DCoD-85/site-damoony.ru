<table class="table">
    <thead>
        <tr>
            <?PHP foreach ($resource['fields'] as $name => $field): ?>
                <th><?= htmlspecialchars($field['label'] ?? $name) ?></th>
            <?PHP endforeach; ?>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?PHP if (empty($rows)): ?>
            <tr><td colspan="<?= count($resource['fields']) + 1 ?>">Нет записей.</td></tr>
        <?PHP else: ?>
            <?PHP foreach ($rows as $row): ?>
                <tr>
                                        <?PHP foreach ($resource['fields'] as $name => $field): ?>
                        <td>
                            <?PHP if ($field['type'] === 'boolean'): ?>
                                <?= $row[$name] ? 'Да' : 'Нет' ?>
                            <?PHP elseif ($field['type'] === 'relation'): ?>
                                <?PHP 
                                    $displayVal = '—';
                                    $currentId = (int)($row[$name] ?? 0);
                                    foreach ($relations[$name] ?? [] as $opt) {
                                        if ((int)$opt['id'] === $currentId) { $displayVal = $opt['label']; break; }
                                    }
                                ?>
                                <?= htmlspecialchars($displayVal) ?>
                            <?PHP else: ?>
                                <?= htmlspecialchars(mb_strimwidth((string)($row[$name] ?? ''), 0, 50, '...')) ?>
                            <?PHP endif; ?>
                        </td>
                    <?PHP endforeach; ?>
                    <td>
                        <a href="?p=resource:form&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&resource=<?= urlencode($resource_id) ?>&id=<?= (int)$row['id'] ?>" class="btn-edit">Ред.</a>
                    </td>
                </tr>
            <?PHP endforeach; ?>
        <?PHP endif; ?>
    </tbody>
</table>