<?PHP if ( ! empty( $saved ) && empty( $generated_invite ) ) : ?>
    <div class="ok-msg">✓ Сохранено</div>
<?PHP endif ?>

<div class="nav-tabs" style="margin-bottom:20px;">
    <a href="?p=security&tab=policy" class="<?= $tab === 'policy' ? 'active' : '' ?>">Политика</a>
    <a href="?p=security&tab=users" class="<?= $tab === 'users' ? 'active' : '' ?>">Пользователи и устройства</a>
    <a href="?p=security&tab=logs" class="<?= $tab === 'logs' ? 'active' : '' ?>">Логи</a>
</div>

<?PHP if ( $tab === 'policy' ) : ?>
    <form method="post" action="?p=security&tab=policy" class="form" style="max-width: 600px;">
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
                                <li>
                                    <b><?= htmlspecialchars($d['label'] ?? 'Unknown') ?></b>
                                    <span style="color:#888;">
                                        (<?= htmlspecialchars($d['last_ip'] ?? '—') ?><?PHP if ( ! empty( $d['last_seen'] ) ) : ?>
                                            · <?= date( 'd.m.Y H:i', (int) $d['last_seen'] ) ?>
                                        <?PHP endif ?>)
                                    </span>
                                </li>
                            <?PHP endforeach ?>
                            </ul>
                        <?PHP endif ?>
                    </td>
                    <td>
                        <?PHP $is_self = ( ( $_SESSION['admin_login'] ?? '' ) === $login ); ?>
                        <form method="post" action="?p=security&tab=users" style="display:inline;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="reset_devices">
                            <input type="hidden" name="login" value="<?= htmlspecialchars($login) ?>">
                            <button type="submit" class="btn-danger"
                                    <?= $is_self ? 'disabled title="Нельзя сбросить свои устройства: вы потеряете доступ"' : 'onclick="return confirm(\'Сбросить все устройства?\')"' ?>>Сбросить</button>
                        </form>
                    </td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>

    <h3 style="margin-top:40px;">Приглашения</h3>
    <?PHP if (!empty($generated_invite)) : ?>
        <div class="ok-msg">
            Ссылка для приглашения:<br>
            <div style="display:flex; gap:8px; margin-top:8px;">
                <input type="text" value="<?= htmlspecialchars($generated_invite) ?>" style="flex:1;" readonly onclick="this.select()">
                <button type="button" class="btn-edit" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✓'; setTimeout(()=>this.textContent='Копировать', 1500);">Копировать</button>
            </div>
        </div>
    <?PHP endif ?>
    
    <form method="post" action="?p=security&tab=users" class="form" style="margin-bottom:20px;">
        <?= Csrf::field() ?>
        <input type="hidden" name="action" value="create_invite">
        
        <label>Тип приглашения
            <select name="invite_type" id="invite_type" onchange="toggleInviteType()">
                <option value="new_user">Новый пользователь (создать аккаунт)</option>
                <option value="bind_user">Существующий пользователь (привязать устройство)</option>
            </select>
        </label>

        <div id="row-role">
            <label>Роль нового пользователя
                <select name="role">
                    <option value="admin">Администратор</option>
                    <option value="super">Супер-администратор</option>
                </select>
            </label>
        </div>

        <div id="row-target-user" style="display:none;">
            <label>Для кого (логин)
                <select name="target_user">
                    <?PHP foreach ( $admins['users'] ?? [] as $login => $u ) : ?>
                        <option value="<?= htmlspecialchars($login) ?>"><?= htmlspecialchars($login) ?> (<?= htmlspecialchars($u['role'] ?? 'admin') ?>)</option>
                    <?PHP endforeach ?>
                </select>
            </label>
        </div>

        <button type="submit" class="btn-primary">Создать приглашение</button>
    </form>

    <script>
    function toggleInviteType() {
        var type = document.getElementById('invite_type').value;
        document.getElementById('row-role').style.display = (type === 'new_user') ? 'block' : 'none';
        document.getElementById('row-target-user').style.display = (type === 'bind_user') ? 'block' : 'none';
    }
    </script>

    <table class="table">
        <thead>
            <tr>
                <th>Токен</th>
                <th>Роль / Тип</th>
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
                        <td>
                            <?PHP if ( ($inv['role'] ?? '') === 'bind_only' ) : ?>
                                Привязка: <b><?= htmlspecialchars($inv['target_user'] ?? '?') ?></b>
                            <?PHP else : ?>
                                <?= htmlspecialchars($inv['role'] ?? 'admin') ?>
                            <?PHP endif ?>
                        </td>
                        <td>
                            <?PHP if ( ! empty( $inv['expires_at'] ) ) : ?>
                                <?= date('d.m.Y H:i', (int) $inv['expires_at']) ?>
                            <?PHP else : ?>
                                —
                            <?PHP endif ?>
                        </td>
                        <td>
                            <?PHP if (!empty($inv['used'])) : ?>
                                <span style="color:#888;">Использован</span>
                            <?PHP elseif ( empty( $inv['expires_at'] ) || $inv['expires_at'] < time() ) : ?>
                                <span style="color:#c00;">Истёк</span>
                            <?PHP else : ?>
                                <span style="color:#0a0; font-weight:bold;">Активен</span>
                            <?PHP endif ?>
                        </td>
                        <td>
                            <form method="post" action="?p=security&tab=users">
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