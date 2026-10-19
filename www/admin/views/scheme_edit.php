<?php
// Рекурсивная отрисовка узла
function render_node ( $n, $domain, $is_root = false ) {
    $class   = $n['class']   ?? 'node';
    $layout  = $n['layout']  ?? 'vertical';
    $name    = str_replace('dt_', '', $class) ?: $class;
    $enabled = $n['enabled'] ?? true;
    
    $checked   = $enabled ? 'checked' : '';
    $sel_v     = $layout === 'vertical'   ? 'selected' : '';
    $sel_h     = $layout === 'horizontal' ? 'selected' : '';
    $draggable = $is_root ? 'false' : 'true';
    $handle    = $is_root ? '<span style="width:16px; display:inline-block;"></span>' : '<span class="drag-handle">≡</span>';
    
    $root_class = $is_root ? ' tree-root' : '';
    $badge      = $is_root ? ' <span class="root-badge">(Контейнер)</span>' : '';
    
    // Проверка наличия файлов блока
    $is_layout = ($n['type'] ?? 'block') === 'layout';
    $block_exists = true;
    if ( $is_layout ) {
        $badge = ' <span style="color:#888; font-size:11px;">(Визуальный)</span>';
    } elseif ( ! $is_root && $class !== 'dt' ) {
        $block_name = str_replace('dt_', '', $class);
        if ( ! is_dir( Paths::block( $domain , $block_name ) ) ) {
            $block_exists = false;
            $badge = ' <span style="color:#a11; font-size:11px;">(удалён)</span>';
        }
    }

    echo "<li class='tree-node{$root_class} ' . (!$block_exists ? 'block-missing' : '') . '' data-class='{$class}'>";
    echo "  <div class='node-header' draggable='{$draggable}'>";
    echo "    {$handle}";
    echo "    <label><input type='checkbox' class='node-enabled' {$checked} " . ($is_root ? 'disabled' : '') . "> <b>{$name}</b>{$badge}</label>";
    echo "    <select class='node-layout' style='margin-left:10px; font-size:12px; padding:2px; border-radius:3px; border:1px solid #ccc;'>";
    echo "      <option value='vertical' {$sel_v}>↓ Вертикально</option>";
    echo "      <option value='horizontal' {$sel_h}>→ Горизонтально</option>";
    echo "    </select>";
    echo "    <code class='node-code'>{$class}</code>";
    echo "  </div>";
    
    if ( !empty($n['children']) ) {
        echo "<ul class='scheme-tree'>";
        foreach ( $n['children'] as $child ) {
            render_node( $child, $domain, false );
        }
        echo "</ul>";
    } else {
        echo "<ul class='scheme-tree' style='min-height:10px;'></ul>";
    }
    echo "</li>";
}

// Рекурсивная отрисовка Wireframe
function render_wireframe ( $nodes ) {
    foreach ( $nodes as $n ) {
        $class   = $n['class']   ?? '';
        if ( !$class ) continue;
        $enabled = $n['enabled'] ?? true;
        $layout  = $n['layout']  ?? 'vertical';
        $name    = str_replace('dt_', '', $class) ?: $class;

        if ( $enabled ) {
            echo "<div style='display:flex; flex-direction:column; flex:1; box-sizing:border-box; border:2px dashed #2a63d6; background:rgba(42, 99, 214, 0.05); padding:10px; border-radius:6px; margin:4px; min-width:80px; text-align:center;'>";
            echo "<div style='font-size:12px; color:#2a63d6; font-weight:bold; margin-bottom:8px;'>$name</div>";
            
            if ( !empty($n['children']) ) {
                $flex_dir = $layout === 'horizontal' ? 'row' : 'column';
                echo "<div style='display:flex; flex-direction:$flex_dir; gap:8px; flex:1; align-items:stretch;'>";
                render_wireframe( $n['children'] );
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<div style='display:flex; flex-direction:column; flex:1; box-sizing:border-box; border:2px solid #ddd; background:#fafafa; padding:10px; border-radius:6px; margin:4px; color:#aaa; font-size:12px; text-align:center;'>$name (выкл)</div>";
        }
    }
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h1 style="margin:0;">Схема: <code><?= htmlspecialchars( $scheme ) ?></code></h1>
    <a href="?p=blocks:list&domain=<?= urlencode($domain) ?>" class="btn-cancel">← Назад</a>
</div>

<?php if ( !empty($_GET['saved']) ) : ?><div class="ok-msg">✓ Сохранено</div><?php endif ?>

<form method="post" id="scheme-form" style="display:flex; gap:20px; align-items:flex-start;">
    <?= Csrf::field() ?>
    <input type="hidden" name="scheme_json" id="scheme_json">
    
    <div style="flex:1; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="margin-top:0;">Дерево блоков</h3>
        <p class="hint" style="font-size:12px; color:#666; margin-bottom:15px;">
            Перетаскивайте за <b>≡</b>. Верхняя/нижняя часть блока — вставка рядом. Центр — вложение внутрь.
        </p>
        
        <?php render_node( $scheme_data, $domain, true ); ?>
        
        <button type="submit" style="margin-top:20px;">Сохранить схему</button>
    </div>

    <div style="flex:2; background:#fff; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
        <h3 style="margin-top:0;">Визуализация</h3>
		<div style="display:flex; flex:1; min-height:200px; box-sizing:border-box;">
			<?php render_wireframe( [ $scheme_data ] ); ?>
		</div>
    </div>
</form>

<style>
/* Стили для корневого контейнера */
.tree-root {
    background: #eef2f6;
    border: 2px solid #c9cdd6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 0;
}
.tree-root > .scheme-tree {
    padding-left: 10px;
    border-left: none;
    margin-top: 10px;
}
.tree-root > .node-header {
    background: #fff;
    border-bottom: 3px solid #2a63d6;
    cursor: default;
}
.root-badge {
    font-size: 11px;
    color: #555;
    font-weight: normal;
    margin-left: 5px;
    background: #dde3ec;
    padding: 2px 6px;
    border-radius: 4px;
}

/* Стили для обычных узлов */
.scheme-tree { list-style: none; padding-left: 20px; margin: 0; border-left: 2px dashed #c9cdd6; min-height: 10px; }
.tree-node { margin: 6px 0; position: relative; }
.node-header { display: flex; align-items: center; gap: 8px; background: #fff; padding: 8px 12px; border: 1px solid #e0e4eb; border-radius: 4px; cursor: grab; }
.node-header:active { cursor: grabbing; }
.drag-handle { color: #999; font-size: 16px; cursor: grab; }
.node-code { font-size: 11px; color: #888; margin-left: auto; }
.dragging { opacity: 0.5; }
.block-missing > .node-header { background: #fdf2f2; border-color: #f5c6c6; color: #a11; }

/* Зоны сброса */
.drop-above { border-top: 3px solid #2a63d6 !important; }
.drop-below { border-bottom: 3px solid #2a63d6 !important; }
.drop-inside > .node-header { background: #e3f0ff !important; border-color: #2a63d6 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let dragged = null;

    // 1. Настройка перетаскивания (только за иконку ≡)
    document.querySelectorAll('.node-header[draggable="true"]').forEach(header => {
        const handle = header.querySelector('.drag-handle');
        
        // Разрешаем drag только при клике на ручку
        if (handle) {
            handle.addEventListener('mousedown', () => { header.setAttribute('draggable', 'true'); });
            header.addEventListener('mouseup', () => { if(!dragged) header.setAttribute('draggable', 'false'); });
            header.addEventListener('mouseleave', () => { if(!dragged) header.setAttribute('draggable', 'false'); });
        }

        header.addEventListener('dragstart', function(e) {
            dragged = this.closest('.tree-node');
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', 'drag');
        });

        header.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            dragged = null;
            document.querySelectorAll('.tree-node').forEach(el => {
                el.classList.remove('drop-above', 'drop-below', 'drop-inside');
            });
        });
    });

    // 2. Обработка зон сброса (вешаем прямо на header, чтобы высота была правильной)
    document.querySelectorAll('.node-header').forEach(header => {
        header.addEventListener('dragover', function(e) {
            e.preventDefault();
            const li = this.closest('.tree-node');
            if (li === dragged || li.contains(dragged)) return;
            
            // Считаем координаты именно ЗАГОЛОВКА, а не всего <li>
            const rect = this.getBoundingClientRect();
            const offset = e.clientY - rect.top;
            
            document.querySelectorAll('.tree-node').forEach(el => {
                el.classList.remove('drop-above', 'drop-below', 'drop-inside');
            });

            // 30% сверху, 30% снизу, 40% по центру
            if (offset < rect.height * 0.3) {
                li.classList.add('drop-above');
            } else if (offset > rect.height * 0.7) {
                li.classList.add('drop-below');
            } else {
                li.classList.add('drop-inside');
            }
        });

        header.addEventListener('dragleave', function(e) {
            const li = this.closest('.tree-node');
            if (!li.contains(e.relatedTarget)) {
                li.classList.remove('drop-above', 'drop-below', 'drop-inside');
            }
        });

        header.addEventListener('drop', function(e) {
            e.preventDefault();
            const li = this.closest('.tree-node');
            li.classList.remove('drop-above', 'drop-below', 'drop-inside');
            
            if (li === dragged || li.contains(dragged)) return;

            const rect = this.getBoundingClientRect();
            const offset = e.clientY - rect.top;

            if (offset < rect.height * 0.3) {
                li.parentNode.insertBefore(dragged, li); // Вставить выше
            } else if (offset > rect.height * 0.7) {
                li.parentNode.insertBefore(dragged, li.nextSibling); // Вставить ниже
            } else {
                let ul = li.querySelector(':scope > .scheme-tree'); // Вложить внутрь
                if (!ul) {
                    ul = document.createElement('ul');
                    ul.className = 'scheme-tree';
                    li.appendChild(ul);
                }
                ul.appendChild(dragged);
            }
            dragged = null;
        });
    });

    // 3. Сборка JSON перед отправкой
    document.getElementById('scheme-form').addEventListener('submit', function(e) {
        function buildChildren(ul) {
            let arr = [];
            if (!ul) return arr;
            ul.querySelectorAll(':scope > .tree-node').forEach(li => {
                let classStr = li.dataset.class;
                let enabled = li.querySelector('.node-enabled').checked;
                let layout = li.querySelector('.node-layout').value;
                let node = { tag: 'div', class: classStr, layout: layout, enabled: enabled };
                
                let childUl = li.querySelector(':scope > .scheme-tree');
                let children = buildChildren(childUl);
                if (children.length > 0) node.children = children;
                arr.push(node);
            });
            return arr;
        }

        function fixReturn(nodes) {
            nodes.forEach(n => {
                if (n.children) {
                    n.return = true;
                    fixReturn(n.children);
                } else {
                    n.return = false;
                }
            });
        }

        let rootNode = document.querySelector('.tree-root');
        let finalData = {
            tag: 'div',
            class: rootNode.dataset.class,
            layout: rootNode.querySelector('.node-layout').value,
            children: buildChildren(rootNode.querySelector(':scope > .scheme-tree'))
        };
        
        fixReturn(finalData.children);
        document.getElementById('scheme_json').value = JSON.stringify(finalData);
    });
});
</script>
