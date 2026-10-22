<?PHP
# ============================================================
#   page_visual.php — визуальный редактор visuo-структуры
#   Формат: {tag, class, children: [...]}
# ============================================================

$initial = json_decode( $json_data , true );
if ( ! is_array( $initial ) || empty( $initial[ 'tag' ] ) ) {
	$initial = [ 'tag' => 'div' , 'class' => 'landing-container' , 'children' => [ ] ];
}

$saved = ! empty( $_GET[ 'saved' ] );

$page_code_url = '?p=page:code&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path );
$page_edit_url = '?p=page:edit&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias ) . '&path=' . urlencode( $path );
$tree_url      = '?p=pages:tree&domain=' . urlencode( $domain ) . '&allias=' . urlencode( $allias );

$crumb_path = $path === '' ? '(корень)' : $path;
?>

<style>
.admin-main { max-width: none !important; padding: 0 !important; margin: 0 !important; }

.visual-page {
	--bg:#0d1117; --bg-2:#161b22; --bg-3:#1f242c; --bg-4:#2d333b;
	--border:#30363d; --text:#c9d1d9; --text-dim:#8b949e;
	--accent:#58a6ff; --accent-hi:#79c0ff; --accent-bg:rgba(88,166,255,.12);
	--success:#3fb950; --danger:#f85149; --warn:#d29922;

	display:flex; flex-direction:column; height:calc(100vh - 56px);
	background:var(--bg); color:var(--text);
	font:13px/1.5 -apple-system,"Segoe UI",Roboto,sans-serif; overflow:hidden;
}

.visual-toolbar {
	display:flex; align-items:center; gap:12px; padding:10px 16px;
	background:var(--bg-2); border-bottom:1px solid var(--border); flex-shrink:0;
}
.vt-left,.vt-center,.vt-right{display:flex;align-items:center;gap:6px;}
.vt-center{margin:0 auto;}
.vt-crumb{display:flex;gap:6px;align-items:center;color:var(--text-dim);font-size:13px;}
.vt-crumb a{color:var(--text-dim);text-decoration:none;}
.vt-crumb a:hover{color:var(--accent);}
.vt-crumb .sep{color:var(--bg-4);}
.vt-crumb .cur{color:var(--text);font-weight:500;}

.vt-btn {
	display:inline-flex; align-items:center; gap:4px; padding:5px 10px;
	background:var(--bg-3); border:1px solid var(--border); color:var(--text);
	font:12px/1 inherit; border-radius:5px; cursor:pointer; text-decoration:none;
	transition:all .15s; white-space:nowrap;
}
.vt-btn:hover{background:var(--bg-4);border-color:#444c56;}
.vt-btn:disabled{opacity:.4;cursor:not-allowed;}
.vt-btn-primary{background:var(--accent);border-color:var(--accent);color:#0d1117;font-weight:600;}
.vt-btn-primary:hover{background:var(--accent-hi);border-color:var(--accent-hi);}
.vt-preview{color:var(--success);}

.vt-toast {
	position:fixed; top:68px; right:24px; background:var(--success); color:#0d1117;
	padding:8px 16px; border-radius:6px; font-size:13px; font-weight:600;
	z-index:100; animation:slideIn .3s ease, fadeOut .5s ease 2.5s forwards;
	pointer-events:none;
}
@keyframes slideIn{from{transform:translateY(-20px);opacity:0;}to{transform:translateY(0);opacity:1;}}
@keyframes fadeOut{to{opacity:0;transform:translateY(-10px);}}

.visual-main{flex:1;display:grid;grid-template-columns:200px 1fr 280px;overflow:hidden;}

.visual-palette{background:var(--bg-2);border-right:1px solid var(--border);overflow-y:auto;padding:12px;}
.vp-title{font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--text-dim);margin:16px 0 8px;font-weight:600;}
.vp-title:first-child{margin-top:0;}
.vp-grid{display:grid;grid-template-columns:1fr 1fr;gap:4px;}
.vp-item{display:flex;flex-direction:column;align-items:center;gap:2px;padding:8px 4px;background:var(--bg-3);border:1px solid var(--border);border-radius:4px;color:var(--text);font:11px/1 monospace;cursor:pointer;transition:all .12s;}
.vp-item:hover{background:var(--accent-bg);border-color:var(--accent);color:var(--accent-hi);}
.vp-item .vp-icon{font-size:14px;opacity:.7;}

.visual-canvas-wrap{background:repeating-linear-gradient(45deg,transparent,transparent 8px,rgba(255,255,255,.01) 8px,rgba(255,255,255,.01) 16px);overflow-y:auto;padding:20px;}
.visual-canvas{max-width:900px;margin:0 auto;}
.canvas-empty{text-align:center;padding:80px 20px;color:var(--text-dim);}
.canvas-empty h3{color:var(--text);margin-bottom:8px;font-size:15px;}

.vnode{background:var(--bg-2);border:1px solid var(--border);border-radius:5px;margin:6px 0;transition:border-color .12s,box-shadow .12s;position:relative;}
.vnode:hover{border-color:#444c56;}
.vnode.selected{border-color:var(--accent);box-shadow:0 0 0 2px var(--accent-bg);}
.vnode-header{display:flex;align-items:center;gap:6px;padding:6px 8px;cursor:pointer;user-select:none;background:var(--bg-3);border-radius:4px 4px 0 0;}
.vnode.selected > .vnode-header{background:var(--accent-bg);}
.vnode-tag{color:var(--accent);font:600 12px/1 ui-monospace,Consolas,monospace;}
.vnode-class{color:var(--warn);font:11px/1 ui-monospace,Consolas,monospace;}
.vnode-id{color:var(--text-dim);font:11px/1 ui-monospace,Consolas,monospace;}
.vnode-actions{margin-left:auto;display:flex;gap:2px;opacity:0;transition:opacity .12s;}
.vnode:hover > .vnode-header > .vnode-actions,
.vnode.selected > .vnode-header > .vnode-actions{opacity:1;}
.vnode-actions button{width:22px;height:22px;padding:0;background:transparent;border:1px solid transparent;color:var(--text-dim);font:12px/1 monospace;border-radius:3px;cursor:pointer;transition:all .12s;}
.vnode-actions button:hover{background:var(--bg-4);color:var(--text);}
.vnode-actions button[data-act="delete"]:hover{background:var(--danger);color:#fff;border-color:var(--danger);}
.vnode-children{padding:4px 8px 8px 16px;border-left:2px dashed var(--bg-4);margin-left:4px;}
.vnode-text{padding:6px 10px;color:var(--text);font-size:12px;font-family:ui-monospace,Consolas,monospace;word-break:break-word;cursor:text;}
.vnode-text:hover{background:var(--bg-3);border-radius:3px;}

.visual-props{background:var(--bg-2);border-left:1px solid var(--border);overflow-y:auto;padding:12px;}
.props-empty{color:var(--text-dim);text-align:center;padding:40px 12px;font-size:12px;}
.props-header{font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:var(--text-dim);margin-bottom:10px;font-weight:600;}
.prop-field{margin-bottom:10px;}
.prop-field label{display:block;font-size:11px;color:var(--text-dim);margin-bottom:3px;text-transform:uppercase;letter-spacing:.3px;}
.prop-field input,.prop-field textarea,.prop-field select{width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:4px;color:var(--text);font:12px/1.4 ui-monospace,Consolas,monospace;box-sizing:border-box;transition:border-color .12s;}
.prop-field input:focus,.prop-field textarea:focus,.prop-field select:focus{outline:none;border-color:var(--accent);}
.prop-field textarea{resize:vertical;min-height:60px;}
.prop-section{border-top:1px solid var(--border);margin-top:12px;padding-top:12px;}
.prop-section-title{font-size:10px;text-transform:uppercase;color:var(--text-dim);letter-spacing:.5px;margin-bottom:8px;}
</style>

<div class="visual-page">

	<?php if ( $saved ) : ?><div class="vt-toast">✓ Сохранено</div><?php endif ?>

	<div class="visual-toolbar">
		<div class="vt-left">
			<a href="<?= htmlspecialchars( $tree_url , ENT_QUOTES ) ?>" class="vt-btn">← Назад</a>
			<div class="vt-crumb">
				<a href="?p=dashboard">Домены</a><span class="sep">/</span>
				<a href="?p=domain:edit&domain=<?= urlencode( $domain ) ?>"><?= htmlspecialchars( $domain ) ?></a><span class="sep">/</span>
				<a href="<?= htmlspecialchars( $tree_url , ENT_QUOTES ) ?>"><?= htmlspecialchars( $allias ) ?></a><span class="sep">/</span>
				<span class="cur"><?= htmlspecialchars( $crumb_path ) ?></span>
			</div>
		</div>
		<div class="vt-center">
			<button class="vt-btn" id="btn-undo" onclick="VZ.undo()" title="Отменить (Ctrl+Z)" disabled>↶</button>
			<button class="vt-btn" id="btn-redo" onclick="VZ.redo()" title="Повторить (Ctrl+Y)" disabled>↷</button>
		</div>
		<div class="vt-right">
			<a href="<?= htmlspecialchars( $preview_url , ENT_QUOTES ) ?>" target="_blank" class="vt-btn vt-preview" title="Превью в новой вкладке">👁 Превью</a>
			<a href="<?= htmlspecialchars( $page_code_url , ENT_QUOTES ) ?>" class="vt-btn">Код</a>
			<a href="<?= htmlspecialchars( $page_edit_url , ENT_QUOTES ) ?>" class="vt-btn">Настройки</a>
			<button class="vt-btn vt-btn-primary" onclick="VZ.save()" title="Ctrl+S">💾 Сохранить</button>
		</div>
	</div>

	<div class="visual-main">
		<aside class="visual-palette">
			<div class="vp-title">Контейнеры</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="div">▢ div</button>
				<button class="vp-item" data-tag="section">▤ section</button>
				<button class="vp-item" data-tag="article">📄 article</button>
				<button class="vp-item" data-tag="header">⬒ header</button>
				<button class="vp-item" data-tag="footer">⬓ footer</button>
				<button class="vp-item" data-tag="nav">☰ nav</button>
				<button class="vp-item" data-tag="main">◱ main</button>
				<button class="vp-item" data-tag="aside">◲ aside</button>
			</div>
			<div class="vp-title">Текст</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="h1">H1</button>
				<button class="vp-item" data-tag="h2">H2</button>
				<button class="vp-item" data-tag="h3">H3</button>
				<button class="vp-item" data-tag="h4">H4</button>
				<button class="vp-item" data-tag="p">¶ p</button>
				<button class="vp-item" data-tag="span">T span</button>
				<button class="vp-item" data-tag="strong">B</button>
				<button class="vp-item" data-tag="em">I</button>
			</div>
			<div class="vp-title">Списки</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="ul">• ul</button>
				<button class="vp-item" data-tag="ol">1. ol</button>
				<button class="vp-item" data-tag="li">– li</button>
			</div>
			<div class="vp-title">Медиа</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="img">🖼 img</button>
				<button class="vp-item" data-tag="video">▶ video</button>
				<button class="vp-item" data-tag="audio">🔊 audio</button>
			</div>
			<div class="vp-title">Ссылки</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="a">🔗 a</button>
				<button class="vp-item" data-tag="button">🔘 button</button>
			</div>
			<div class="vp-title">Формы</div>
			<div class="vp-grid">
				<button class="vp-item" data-tag="form">📝 form</button>
				<button class="vp-item" data-tag="input">⌨ input</button>
				<button class="vp-item" data-tag="textarea">▤ textarea</button>
				<button class="vp-item" data-tag="select">▾ select</button>
			</div>
		</aside>

		<div class="visual-canvas-wrap">
			<div class="visual-canvas" id="canvas"></div>
		</div>

		<aside class="visual-props">
			<div class="props-header">Свойства</div>
			<div id="props"></div>
		</aside>
	</div>

	<form method="post" id="save-form" style="display:none;">
		<?= Csrf::field() ?>
		<input type="hidden" name="json_code" id="json-code">
	</form>
</div>

<script>
window.VZ = (function () {
	'use strict';

	const initialTree = <?= json_encode( $initial , JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_UNESCAPED_UNICODE ) ?>;

	let tree = null;
	let nextId = 1;
	let selectedId = null;
	let history = [];
	let historyIdx = -1;
	let debounceTimer = null;

	function assignIds(node) {
		if (!node || typeof node !== 'object') return node;
		node._id = nextId++;
		if (Array.isArray(node.children)) node.children = node.children.map(assignIds);
		return node;
	}
	function stripIds(node) {
		if (!node || typeof node !== 'object') return node;
		const c = {};
		for (const k in node) {
			if (k === '_id') continue;
			if (k === 'children') c.children = Array.isArray(node.children) ? node.children.map(stripIds) : [];
			else c[k] = node[k];
		}
		return c;
	}
	function findNode(node, id) {
		if (!node) return null;
		if (node._id === id) return node;
		if (Array.isArray(node.children)) for (const c of node.children) { const r = findNode(c, id); if (r) return r; }
		return null;
	}
	function findParent(node, id) {
		if (!node || !Array.isArray(node.children)) return null;
		for (const c of node.children) { if (c._id === id) return node; const r = findParent(c, id); if (r) return r; }
		return null;
	}

	function saveSnapshot() {
		const snap = JSON.stringify(stripIds(tree));
		if (history[historyIdx] === snap) return false;
		history = history.slice(0, historyIdx + 1);
		history.push(snap);
		if (history.length > 100) history.shift();
		historyIdx = history.length - 1;
		updateHistoryButtons();
		return true;
	}
	function updateHistoryButtons() {
		document.getElementById('btn-undo').disabled = historyIdx <= 0;
		document.getElementById('btn-redo').disabled = historyIdx >= history.length - 1;
	}
	function undo() {
		if (historyIdx <= 0) return;
		historyIdx--;
		tree = assignIds(JSON.parse(history[historyIdx]));
		selectedId = null;
		render(); updateProps(); updateHistoryButtons();
	}
	function redo() {
		if (historyIdx >= history.length - 1) return;
		historyIdx++;
		tree = assignIds(JSON.parse(history[historyIdx]));
		selectedId = null;
		render(); updateProps(); updateHistoryButtons();
	}

	function render() {
		const canvas = document.getElementById('canvas');
		canvas.innerHTML = '';
		if (!tree) return;
		canvas.appendChild(renderNode(tree, null));
		if ((!tree.children || !tree.children.length) && tree.text === undefined) {
			const empty = document.createElement('div');
			empty.className = 'canvas-empty';
			empty.innerHTML = '<h3>Холст пуст</h3><p>Кликните элемент в палитре слева</p>';
			canvas.insertBefore(empty, canvas.firstChild);
		}
	}

	function renderNode(node, parent) {
		const el = document.createElement('div');
		el.className = 'vnode';
		el.dataset.id = node._id;
		if (node._id === selectedId) el.classList.add('selected');

		const header = document.createElement('div');
		header.className = 'vnode-header';

		const tagSpan = document.createElement('span');
		tagSpan.className = 'vnode-tag';
		tagSpan.textContent = '<' + (node.tag || 'div') + '>';
		header.appendChild(tagSpan);

		if (node.id) {
			const s = document.createElement('span'); s.className = 'vnode-id'; s.textContent = '#' + node.id; header.appendChild(s);
		}
		if (node.class) {
			const s = document.createElement('span'); s.className = 'vnode-class'; s.textContent = '.' + node.class; header.appendChild(s);
		}

		const actions = document.createElement('span');
		actions.className = 'vnode-actions';
		let html = '';
		if (parent) html += '<button data-act="up" title="Вверх">↑</button><button data-act="down" title="Вниз">↓</button>';
		html += '<button data-act="add" title="Добавить внутрь">+</button>';
		if (parent) html += '<button data-act="delete" title="Удалить">×</button>';
		actions.innerHTML = html;
		header.appendChild(actions);

		header.addEventListener('click', (e) => {
			const btn = e.target.closest('button');
			if (btn) { e.stopPropagation(); handleAction(btn.dataset.act, node, parent); }
			else selectNode(node._id);
		});
		el.appendChild(header);

		if (Array.isArray(node.children) && node.children.length) {
			const wrap = document.createElement('div');
			wrap.className = 'vnode-children';
			node.children.forEach(c => wrap.appendChild(renderNode(c, node)));
			el.appendChild(wrap);
		} else if (node.text !== undefined) {
			const t = document.createElement('div');
			t.className = 'vnode-text';
			t.textContent = node.text;
			t.title = 'Двойной клик для редактирования';
			t.addEventListener('dblclick', (e) => {
				e.stopPropagation();
				const v = prompt('Текст:', node.text || '');
				if (v !== null) { node.text = v; saveSnapshot(); t.textContent = v; }
			});
			el.appendChild(t);
		}

		return el;
	}

	function selectNode(id) { selectedId = id; render(); updateProps(); }

	function handleAction(act, node, parent) {
		if (act === 'delete') {
			if (!parent || !confirm('Удалить <' + (node.tag || 'div') + '>?')) return;
			const i = parent.children.indexOf(node);
			if (i >= 0) parent.children.splice(i, 1);
			if (selectedId === node._id) selectedId = null;
			commit();
		} else if (act === 'up' || act === 'down') {
			if (!parent) return;
			const i = parent.children.indexOf(node);
			const ni = act === 'up' ? i - 1 : i + 1;
			if (ni < 0 || ni >= parent.children.length) return;
			[parent.children[i], parent.children[ni]] = [parent.children[ni], parent.children[i]];
			commit();
		} else if (act === 'add') {
			selectNode(node._id);
		}
	}

	function addFromPalette(tag) {
		const n = { tag };
		if (['h1','h2','h3','h4','h5','h6','p','span','strong','em','label'].includes(tag)) n.text = 'Текст';
		else if (tag === 'a') { n.text = 'Ссылка'; n.href = '#'; }
		else if (tag === 'button') n.text = 'Кнопка';
		else if (tag === 'img') { n.src = ''; n.alt = ''; }
		else if (tag === 'input') n.type = 'text';
		else if (tag === 'form') { n.action = ''; n.method = 'post'; }
		else if (['ul','ol','select'].includes(tag)) n.children = [];

		n._id = nextId++;

		if (selectedId) {
			const t = findNode(tree, selectedId);
			if (t) { t.children = t.children || []; t.children.push(n); }
		} else {
			tree.children = tree.children || [];
			tree.children.push(n);
		}
		selectedId = n._id;
		commit();
	}

	function updateProps() {
		const props = document.getElementById('props');
		if (!selectedId) { props.innerHTML = '<div class="props-empty">Выберите элемент на холсте</div>'; return; }
		const node = findNode(tree, selectedId);
		if (!node) { props.innerHTML = '<div class="props-empty">Не найден</div>'; return; }

		let html = '';
		html += field('tag', 'Тег', node.tag || 'div');
		html += field('id', 'ID', node.id || '');
		html += field('class', 'Class', node.class || '');
		html += field('text', 'Текст', node.text !== undefined ? node.text : '', 'textarea');
		html += field('style', 'Style (inline)', node.style || '');

		if (node.tag === 'img') {
			html += '<div class="prop-section"><div class="prop-section-title">Изображение</div>';
			html += field('src', 'src', node.src || '');
			html += field('alt', 'alt', node.alt || '');
			html += '</div>';
		}
		if (node.tag === 'a') {
			html += '<div class="prop-section"><div class="prop-section-title">Ссылка</div>';
			html += field('href', 'href', node.href || '');
			html += field('target', 'target', node.target || '');
			html += '</div>';
		}
		if (node.tag === 'input') {
			html += '<div class="prop-section"><div class="prop-section-title">Input</div>';
			html += field('type', 'type', node.type || 'text');
			html += field('name', 'name', node.name || '');
			html += field('placeholder', 'placeholder', node.placeholder || '');
			html += '</div>';
		}
		if (node.tag === 'form') {
			html += '<div class="prop-section"><div class="prop-section-title">Form</div>';
			html += field('action', 'action', node.action || '');
			html += field('method', 'method', node.method || 'post', 'select', ['get','post']);
			html += '</div>';
		}

		props.innerHTML = html;

		props.querySelectorAll('input, textarea, select').forEach(inp => {
			inp.addEventListener('input', () => {
				const k = inp.dataset.key;
				const v = inp.value;
				if (v === '') delete node[k]; else node[k] = v;
				updateNodeLabel(node);
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(saveSnapshot, 400);
			});
		});
	}

	function field(key, label, value, type, opts) {
		type = type || 'text';
		if (type === 'textarea') return `<div class="prop-field"><label>${label}</label><textarea data-key="${key}" rows="3">${escapeHtml(value)}</textarea></div>`;
		if (type === 'select') {
			const o = (opts || []).map(x => `<option value="${x}"${x === value ? ' selected' : ''}>${x}</option>`).join('');
			return `<div class="prop-field"><label>${label}</label><select data-key="${key}">${o}</select></div>`;
		}
		return `<div class="prop-field"><label>${label}</label><input type="text" data-key="${key}" value="${escapeHtml(value)}"></div>`;
	}

	function escapeHtml(s) {
		return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
	}

	function updateNodeLabel(node) {
		const header = document.querySelector('.vnode[data-id="' + node._id + '"] > .vnode-header');
		if (!header) return;
		const tg = header.querySelector('.vnode-tag');
		if (tg) tg.textContent = '<' + (node.tag || 'div') + '>';

		let idEl = header.querySelector('.vnode-id');
		if (node.id) {
			if (!idEl) { idEl = document.createElement('span'); idEl.className = 'vnode-id'; header.querySelector('.vnode-tag').after(idEl); }
			idEl.textContent = '#' + node.id;
		} else if (idEl) idEl.remove();

		let clsEl = header.querySelector('.vnode-class');
		if (node.class) {
			if (!clsEl) { clsEl = document.createElement('span'); clsEl.className = 'vnode-class'; header.querySelector('.vnode-actions').before(clsEl); }
			clsEl.textContent = '.' + node.class;
		} else if (clsEl) clsEl.remove();

		if (node.text !== undefined) {
			const vnode = document.querySelector('.vnode[data-id="' + node._id + '"]');
			let t = vnode.querySelector(':scope > .vnode-text');
			if (!t) { t = document.createElement('div'); t.className = 'vnode-text'; vnode.appendChild(t); }
			t.textContent = node.text;
		}
	}

	function commit() { saveSnapshot(); render(); updateProps(); }
	function save() {
		clearTimeout(debounceTimer);
		saveSnapshot();
		document.getElementById('json-code').value = JSON.stringify(stripIds(tree), null, 2);
		document.getElementById('save-form').submit();
	}

	function init() {
		tree = assignIds(JSON.parse(JSON.stringify(initialTree)));
		history = [JSON.stringify(stripIds(tree))];
		historyIdx = 0;
		updateHistoryButtons();
		render(); updateProps();

		document.querySelectorAll('.vp-item').forEach(b => b.addEventListener('click', () => addFromPalette(b.dataset.tag)));

		document.addEventListener('keydown', (e) => {
			if (e.ctrlKey || e.metaKey) {
				if (e.key === 'z') { e.preventDefault(); undo(); }
				if (e.key === 'y' || (e.shiftKey && e.key === 'Z')) { e.preventDefault(); redo(); }
				if (e.key === 's') { e.preventDefault(); save(); }
			}
			if (e.key === 'Delete' && selectedId) {
				const p = findParent(tree, selectedId);
				const n = findNode(tree, selectedId);
				if (p && n) { e.preventDefault(); handleAction('delete', n, p); }
			}
		});
	}

	return { undo, redo, save, init };

})();

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', window.VZ.init);
else window.VZ.init();
</script>
