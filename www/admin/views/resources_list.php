<?PHP if (isset($_GET['synced'])): ?>
    <?PHP if ((int)$_GET['errors'] === 0): ?>
        <div class="ok-msg">✓ Успешно синхронизировано таблиц: <?= (int)$_GET['synced'] ?></div>
    <?PHP else: ?>
        <div class="error-msg">⚠ Синхронизировано: <?= (int)$_GET['synced'] ?>, Ошибок: <?= (int)$_GET['errors'] ?></div>
    <?PHP endif; ?>
<?PHP endif; ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h3>Таблицы домена</h3>
    <form method="post" action="?p=resource:sync&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>" style="margin:0;">
        <?= Csrf::field() ?>
        <button type="submit" class="btn-primary" onclick="return confirm('Синхронизировать структуру БД с файлами ресурсов?')">⚡ Синхронизировать БД</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Таблица в БД</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?PHP foreach ($resources as $r): ?>
            <tr>
                <td><b><?= htmlspecialchars($r['title']) ?></b></td>
                <td><code><?= htmlspecialchars($r['table']) ?></code></td>
                <td>
                    <a href="?p=resource:edit&domain=<?= urlencode($domain) ?>&allias=<?= urlencode($allias) ?>&resource=<?= urlencode($r['id']) ?>" class="btn-edit">Управление записями</a>
                </td>
            </tr>
        <?PHP endforeach; ?>
        <?PHP if (empty($resources)): ?>
            <tr><td colspan="3">Ресурсы не найдены. Создайте файл <code>*.resource.php</code>.</td></tr>
        <?PHP endif; ?>
    </tbody>
</table>