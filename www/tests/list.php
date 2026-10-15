<?PHP

# ============================================================
#   list.php — каталог доступных тестов.
#   Сканирует cases/ и archive/, выдаёт ссылки.
# ============================================================

error_reporting( E_ALL );
ini_set( 'display_errors' , '1' );
header( 'Content-Type: text/html; charset=UTF-8' );

$cases_dir   = __DIR__ . '/cases';
$archive_dir = __DIR__ . '/archive';

# --- discover new cases ---
$new = [ ];
foreach ( glob( $cases_dir . '/*.php' ) as $path ) {
    $name  = basename( $path , '.php' );
    $count = 0;

    $spec = @include $path;
    if ( is_array( $spec ) && isset( $spec[ 'tests' ] ) ) {
        $count = count( $spec[ 'tests' ] );
    }

    $new[ $name ] = [ 'tests' => $count , 'file' => 'cases/' . $name . '.php' ];
}
ksort( $new );

# --- archive ---
$arc = [ ];
foreach ( glob( $archive_dir . '/*.php' ) as $path ) {
    $name = basename( $path , '.php' );
    $arc[ $name ] = 'archive/' . $name . '.php';
}
ksort( $arc );

?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Visuo tests — список</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, "Segoe UI", sans-serif; margin: 0; background: #eef1f5; color: #222; }
        .page_header { padding: 16px 24px; background: #1e2a3a; color: #fff; display: flex; justify-content: space-between; align-items: center; }
        .page_header h1 { margin: 0; font-size: 18px; }
        .page_header a { color: #a6b1c2; text-decoration: none; font-size: 13px; }
        .page_header a:hover { color: #fff; }
        .page_wrap { padding: 20px; max-width: 1000px; margin: 0 auto; }
        h2 { font-size: 15px; color: #4a5568; margin: 24px 0 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        h2:first-child { margin-top: 0; }
        table { width: 100%; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; border-collapse: collapse; }
        th, td { padding: 10px 14px; text-align: left; border-bottom: 1px solid #eef1f5; }
        th { background: #f7f8fa; font-size: 12px; color: #4a5568; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafbfc; }
        .suite-name { font-weight: 600; color: #1e2a3a; }
        .suite-count { color: #8a94a4; font-size: 13px; }
        .btn { display: inline-block; padding: 5px 12px; background: #069; color: #fff; border-radius: 4px; text-decoration: none; font-size: 12px; margin-right: 6px; }
        .btn:hover { background: #057; }
        .btn-alt { background: #f0f0f0; color: #333; }
        .btn-alt:hover { background: #e0e8f0; color: #069; }
        .empty { padding: 16px; color: #8a94a4; font-style: italic; }
    </style>
</head>
<body>

<div class="page_header">
    <h1>Visuo tests — список</h1>
    <a href="index.php">Вкладками →</a>
</div>

<div class="page_wrap">

    <h2>Активные тесты (новый раннер)</h2>
    <?php if ( empty( $new ) ) : ?>
        <div class="empty">Пока нет ни одного suite в <code>cases/</code>.</div>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Suite</th>
                    <th style="width:80px">Кейсов</th>
                    <th style="width:220px">Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $new as $name => $info ) : ?>
                    <tr>
                        <td class="suite-name"><?= htmlspecialchars( $name ) ?></td>
                        <td class="suite-count"><?= (int) $info[ 'tests' ] ?></td>
                        <td>
                            <a class="btn" href="index.php?s=<?= urlencode( $name ) ?>">Открыть</a>
                            <a class="btn btn-alt" href="index.php">Все вкладки</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

    <h2>Архивные тесты (старый формат)</h2>
    <?php if ( empty( $arc ) ) : ?>
        <div class="empty">Архив пуст.</div>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Файл</th>
                    <th style="width:220px">Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $arc as $name => $rel ) : ?>
                    <tr>
                        <td class="suite-name"><?= htmlspecialchars( $name ) ?></td>
                        <td>
                            <a class="btn btn-alt" href="<?= htmlspecialchars( $rel ) ?>" target="_blank">Открыть</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

</div>

</body>
</html>