<?PHP if ( ! empty( $saved ) && empty($generated_invite) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<div class="nav-tabs" style="margin-bottom:20px;">
    <a href="?p=security&tab=policy" class="<?= $tab === 'policy' ? 'active' : '' ?>">Политика</a>
    <a href="?p=security&tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">Пользователи и устройства</a>
    <a href="?p=security&tab=logs" class="<?= $tab === 'logs' ? 'active' : '' ?>">Логи</a>
</div>

<?PHP if ( $tab === 'policy' ) : ?>
    <form method="post" action="?p=security" class="form" style="max-width: 600px;">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="save_policy">
        <h3>Режим доступа</h3>
        <label class="checkbox">
            <input type="radio" name="mode" value="whitelist" <?= ($sec_cfg['mode'] ?? '') === 'whitelist' ? 'checked' : '' ?>>
            Whitelist (только доверенные IP и устройства)
        </label>
        <label class="checkbox">
            <input type="radio" name="mode" value="blacklist" <?= ($sec_cfg['mode'] ?? '') === 'blacklist' ? 'checked' : '' ?>>
            Blacklist (пускать всех, но требовать отпечаток)
        </label>

        <hr style="margin: 20px 0;">
        
        <label class="checkbox">
            <input type="checkbox" name="require_fingerprint" value="1" <?= !empty($sec_cfg['require_fingerprint']) ? 'checked' : '' ?>>
            Требовать отпечаток устройства (Fingerprint)
        </label>

        <button type="submit" class="btn-primary">Сохранить</button>
    </form>

<?PHP elseif ( $tab === 'users' ) : ?>
    <h3>Пользователи</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Логин</th>
                <th>Роль</th>
                <th>Устройства</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?PHP foreach ( $admins['users'] ?? [] as $login => $user ) : ?>
                <tr>
                    <td><b><?= htmlspecialchars($login) ?></b></td>
                    <td><?= htmlspecialchars($user['role'] ?? 'admin') ?></td>
                    <td>
                        <?PHP if (empty($user['devices'])) : ?>
                            <span style="color:#888;">Нет устройств</span>
                        <?PHP else : ?>
                            <ul style="margin:0; padding-left:15px; font-size:13px;">
                            <?PHP foreach ($user['devices'] as $d) : ?>
                                <li><?= htmlspecialchars($d['label']) ?> <span style="color:#888;">(<?= htmlspecialchars($d['last_ip']) ?>)</span></li>
                            <?PHP endforeach ?>
                            </ul>
                        <?PHP endif ?>
                    </td>
                    <td>
                        <form method="post" action="?p=security" style="display:inline;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="reset_devices">
                            <input type="hidden" name="login" value="<?= htmlspecialchars($login) ?>">
                            <button type="submit" class="btn-danger" onclick="return confirm('Сбросить все устройства?')">Сбросить</button>
                        </form>
                    </td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>

    <h3 style="margin-top:40px;">Приглашения</h3>
    <?PHP if (!empty($generated_invite)) : ?>
        <div class="ok-msg">
            Ссылка для приглашения: <br>
            <input type="text" value="<?= htmlspecialchars($generated_invite) ?>" style="width:100%; margin-top:10px;" readonly onclick="this.select()">
        </div>
    <?PHP endif ?>
    <form method="post" action="?p=security" class="form" style="margin-bottom:20px;">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="create_invite">
        <label>Роль
            <select name="role">
                <option value="admin">Администратор</option>
            </select>
        </label>
        <button type="submit" class="btn-primary">Создать приглашение</button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Токен</th>
                <th>Роль</th>
                <th>Действует до</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?PHP if (empty($admins['invites'])) : ?>
                <tr><td colspan="5">Нет активных приглашений.</td></tr>
            <?PHP else : ?>
                <?PHP foreach ( $admins['invites'] ?? [] as $token => $inv ) : ?>
                    <tr>
                        <td><code><?= htmlspecialchars(substr($token, 0, 10)) ?>...</code></td>
                        <td><?= htmlspecialchars($inv['role'] ?? 'admin') ?></td>
                        <td><?= date('d.m.Y H:i', $inv['expires_at']) ?></td>
                        <td>
                            <?PHP if (!empty($inv['used'])) : ?>
                                <span style="color:#888;">Использован</span>
                            <?PHP elseif ($inv['expires_at'] < time()) : ?>
                                <span style="color:#c00;">Истёк</span>
                            <?PHP else : ?>
                                <span style="color:#0a0; font-weight:bold;">Активен</span>
                            <?PHP endif ?>
                        </td>
                        <td>
                            <form method="post" action="?p=security">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="action" value="revoke_invite">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                <button type="submit" class="btn-danger">Отозвать</button>
                            </form>
                        </td>
                    </tr>
                <?PHP endforeach ?>
            <?PHP endif ?>
        </tbody>
    </table>

<?PHP elseif ( $tab === 'logs' ) : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Дата</th>
                <th>IP</th>
                <th>Событие</th>
                <th>Детали</th>
            </tr>
        </thead>
        <tbody>
            <?PHP if (empty($logs)) : ?>
                <tr><td colspan="4">Логов пока нет.</td></tr>
            <?PHP else : ?>
                <?PHP foreach ($logs as $l) : ?>
                    <tr>
                        <td style="font-size:12px; color:#666;"><?= htmlspecialchars($l['ts'] ?? '') ?></td>
                        <td><code><?= htmlspecialchars($l['ip'] ?? '') ?></code></td>
                        <td><b><?= htmlspecialchars($l['event'] ?? '') ?></b></td>
                        <td><small><?= htmlspecialchars(json_encode($l['data'] ?? [], JSON_UNESCAPED_UNICODE)) ?></small></td>
                    </tr>
                <?PHP endforeach ?>
            <?PHP endif ?>
        </tbody>
    </table>
<?PHP endif ?>