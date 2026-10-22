<?PHP if ( empty( $rows ) ) : ?>
    <p>Аллиасов нет.</p>
<?PHP else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Аллиас</th>
                <th>Файлы</th>
                <th>Fillers (вкл/всего)</th>
                <th style="text-align:center;">Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?PHP foreach ( $rows as $r ) : ?>
                <?PHP $a_enc = urlencode( $r[ 'name' ] ); ?>
                <tr style="cursor:pointer;" onclick="window.location='?p=allias:edit&domain=<?= urlencode($domain) ?>&allias=<?= $a_enc ?>';">
                    <td>
                        <a href="?p=allias:edit&domain=<?= urlencode( $domain ) ?>&allias=<?= $a_enc ?>" class="domain-link" style="font-size:16px;">
                            <b><?= htmlspecialchars( $r[ 'name' ] ) ?></b>
                        </a>
                    </td>
                    <td><?= $r[ 'fs' ] ? '✓' : '✗' ?></td>
                    <td><?= (int) $r[ 'fillers_enabled' ] ?> / <?= (int) $r[ 'fillers_total' ] ?></td>
                    <td style="text-align:center;" onclick="event.stopPropagation();">
                        <form method="post" action="?p=allias:toggle" style="margin:0; display:inline-block;">
                            <?= Csrf::field( ) ?>
                            <input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>">
                            <input type="hidden" name="name"   value="<?= htmlspecialchars( $r[ 'name' ] , ENT_QUOTES ) ?>">
                            <input type="hidden" name="value"  value="<?= $r[ 'avail' ] ? '0' : '1' ?>">
                            <label class="switch" title="<?= $r[ 'avail' ] ? 'Выключить аллиас' : 'Включить аллиас' ?>">
                                <input type="checkbox" onchange="this.form.submit()" <?= $r[ 'avail' ] ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </form>
                    </td>
                    <td>
                        <a href="?p=allias:remove&domain=<?= urlencode( $domain ) ?>&name=<?= $a_enc ?>" class="btn-danger" onclick="event.stopPropagation(); return confirm('Удалить аллиас <?= htmlspecialchars($r['name']) ?>?');">Удалить</a>
                    </td>
                </tr>
            <?PHP endforeach ?>
        </tbody>
    </table>
<?PHP endif ?>