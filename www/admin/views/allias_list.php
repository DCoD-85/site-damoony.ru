<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h1 style="margin:0;">Домен: <code><?= htmlspecialchars( $domain , ENT_QUOTES ) ?></code></h1>
    <div class="td-actions">
        <a href="?p=allias:new&domain=<?= urlencode( $domain ) ?>" class="btn-edit">+ Добавить аллиас</a>
        <a href="?p=domain:edit&domain=<?= urlencode( $domain ) ?>" class="btn-edit">Настройки домена</a>
        <a href="?p=dashboard" class="btn-cancel">← К доменам</a>
    </div>
</div>

<?php if ( empty( $rows ) ) : ?>
    <p>Аллиасов нет.</p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Аллиас</th>
                <th>Файлы</th>
                <th>Fillers (вкл/всего)</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $rows as $r ) : ?>
                <?php $a_enc = urlencode( $r[ 'name' ] ); ?>
                <tr>
                    <td>
                        <b><?= htmlspecialchars( $r[ 'name' ] ) ?></b>
                    </td>
                    <td><?= $r[ 'fs' ] ? '✓' : '✗' ?></td>
                    <td><?= (int) $r[ 'fillers_enabled' ] ?> / <?= (int) $r[ 'fillers_total' ] ?></td>
                    <td>
                        <form method="post" action="?p=allias:toggle" class="inline">
                            <?= Csrf::field( ) ?>
                            <input type="hidden" name="domain" value="<?= htmlspecialchars( $domain , ENT_QUOTES ) ?>">
                            <input type="hidden" name="name"   value="<?= htmlspecialchars( $r[ 'name' ] , ENT_QUOTES ) ?>">
                            <input type="hidden" name="value"  value="<?= $r[ 'avail' ] ? '0' : '1' ?>">
                            <button type="submit"
                                class="btn-toggle <?= $r[ 'avail' ] ? 'btn-toggle-on' : 'btn-toggle-off' ?>"
                                title="<?= $r[ 'avail' ] ? 'Выключить' : 'Включить' ?>">
                                <?= $r[ 'avail' ] ? '● вкл' : '○ выкл' ?>
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="td-actions">
                            <a href="?p=pages:tree&domain=<?= urlencode( $domain ) ?>&allias=<?= $a_enc ?>" class="btn-edit">Страницы</a>
                            <a href="?p=allias:fillers&domain=<?= urlencode( $domain ) ?>&allias=<?= $a_enc ?>" class="btn-edit">Fillers</a>
                            <a href="?p=scheme:edit&domain=<?= urlencode( $domain ) ?>&scheme=<?= $a_enc ?>" class="btn-edit">Схема</a>
                            <a href="?p=allias:remove&domain=<?= urlencode( $domain ) ?>&name=<?= $a_enc ?>" class="btn-danger">Удалить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
