<?php
$paginaActual = 'inventario';
$tituloPagina = 'Inventario';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Inventario</div>
    <div class="topbar-actions">
        <button class="btn btn-secondary" onclick="filtrarStock('critico',this)" id="btn-critico">⚠️ Solo stock bajo</button>
        <button class="btn btn-primary" onclick="abrirModalInventario()">+ Nueva refacción</button>
    </div>
</div>

<div class="content">
    <div class="filtros" style="margin-bottom:16px">
        <input type="text" id="buscar-inv" placeholder="🔍 Buscar refacción, n.° parte o proveedor…"
               style="width:320px" oninput="buscarInventario(this.value)">
    </div>
    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre / Descripción</th>
                        <th>N.° parte</th>
                        <th>Stock</th>
                        <th>Mín.</th>
                        <th>P. compra</th>
                        <th>P. venta</th>
                        <th>Proveedor</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-inventario">
                    <tr><td colspan="9"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ Modal Inventario ══ -->
<div class="overlay" id="modal-inventario">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-inv-titulo">Nueva Refacción</span>
        <button class="modal-close" onclick="cerrarModal('modal-inventario')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-inventario" class="form-grid">
            <input type="hidden" name="id_refaccion" id="fi-id">
            <div class="form-group form-full">
                <label class="form-label">Nombre *</label>
                <input class="form-control" name="nombre" id="fi-nombre" required>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Descripción</label>
                <input class="form-control" name="descripcion" id="fi-descripcion">
            </div>
            <div class="form-group">
                <label class="form-label">Número de parte</label>
                <input class="form-control" name="numero_parte" id="fi-numero_parte">
            </div>
            <div class="form-group">
                <label class="form-label">Cantidad en stock *</label>
                <input class="form-control" name="cantidad" id="fi-cantidad" type="number" min="0" required value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Precio de compra *</label>
                <input class="form-control" name="precio_compra" id="fi-precio_compra" type="number" min="0" step="0.01" required value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Precio de venta *</label>
                <input class="form-control" name="precio_venta" id="fi-precio_venta" type="number" min="0" step="0.01" required value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Stock mínimo</label>
                <input class="form-control" name="stock_minimo" id="fi-stock_minimo" type="number" min="0" value="5">
            </div>
            <div class="form-group">
                <label class="form-label">Proveedor</label>
                <input class="form-control" name="proveedor" id="fi-proveedor">
            </div>
            <div class="form-group form-full">
                <label class="form-label">Ubicación en almacén</label>
                <input class="form-control" name="ubicacion" id="fi-ubicacion" placeholder="Ej: Estante A-3">
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-inventario')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarInventario()">💾 Guardar</button>
    </div>
</div>
</div>

<!-- ══ Modal Confirmar Eliminación ══ -->
<div class="overlay" id="modal-confirmar">
<div class="modal" style="max-width:400px">
    <div class="modal-header">
        <span class="modal-title" style="color:var(--danger)">Eliminar Refacción</span>
        <button class="modal-close" onclick="cerrarModal('modal-confirmar')">✕</button>
    </div>
    <div class="modal-body">
        <p style="font-size:14px;line-height:1.6">¿Estás seguro de que deseas eliminar <strong id="confirmar-nombre"></strong>? Esta acción no se puede deshacer.</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-confirmar')">Cancelar</button>
        <button class="btn btn-danger" onclick="confirmarEliminar()">🗑️ Eliminar</button>
    </div>
</div>
</div>

<style>
.stock-bar { display:flex; align-items:center; gap:8px; }
.stock-fill { height:6px; border-radius:3px; background:var(--border); flex:1; overflow:hidden; min-width:50px; }
.stock-fill-inner { height:100%; border-radius:3px; background:var(--success); }
.stock-fill-inner.low { background:var(--warning); }
.stock-fill-inner.empty { background:var(--danger); }
</style>

<script>
let todosItems = [];
let soloStockBajo = false;
let idEliminar = null;

async function cargarInventario() {
    const d = await api({accion:'listar_inventario'});
    todosItems = d.datos || [];
    renderInventario(todosItems);
}

function renderInventario(lista) {
    const tb = document.getElementById('tbody-inventario');
    if (!lista.length) {
        tb.innerHTML = '<tr><td colspan="9"><div class="empty-state"><div class="icon">📦</div><p>No hay refacciones registradas</p></div></td></tr>';
        return;
    }
    tb.innerHTML = lista.map(i => {
        const pct = Math.min(100, Math.round((i.cantidad / (i.stock_minimo * 3 || 1)) * 100));
        const cls = i.cantidad == 0 ? 'empty' : (i.stock_critico == '1' ? 'low' : '');
        return `
        <tr>
            <td>
                <strong>${i.nombre}</strong>
                ${i.descripcion ? `<div style="font-size:11px;color:var(--muted);margin-top:2px">${i.descripcion}</div>` : ''}
                ${i.stock_critico == '1' ? `<span class="badge badge-cancelado" style="margin-top:4px;display:inline-block">⚠ Stock bajo</span>` : ''}
            </td>
            <td class="td-mono">${i.numero_parte||'—'}</td>
            <td>
                <div class="stock-bar">
                    <span style="font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:700;min-width:28px;${i.stock_critico=='1'?'color:var(--danger)':''}">${i.cantidad}</span>
                    <div class="stock-fill">
                        <div class="stock-fill-inner ${cls}" style="width:${pct}%"></div>
                    </div>
                </div>
            </td>
            <td class="td-muted">${i.stock_minimo}</td>
            <td class="td-mono">$${Number(i.precio_compra).toLocaleString('es-MX',{minimumFractionDigits:2})}</td>
            <td class="td-mono">$${Number(i.precio_venta).toLocaleString('es-MX',{minimumFractionDigits:2})}</td>
            <td class="td-muted">${i.proveedor||'—'}</td>
            <td class="td-muted">${i.ubicacion||'—'}</td>
            <td>
                <div style="display:flex;gap:4px">
                    <button class="btn btn-secondary btn-sm" onclick='editarInventario(${JSON.stringify(i)})'>✏️</button>
                    <button class="btn btn-danger btn-sm" onclick="pedirEliminar(${i.id_refaccion},'${i.nombre.replace(/'/g,"\\'")}')">🗑️</button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function buscarInventario(q) {
    let lista = todosItems;
    if (soloStockBajo) lista = lista.filter(i => i.stock_critico == '1');
    if (q) {
        const t = q.toLowerCase();
        lista = lista.filter(i =>
            i.nombre.toLowerCase().includes(t) ||
            (i.numero_parte||'').toLowerCase().includes(t) ||
            (i.proveedor||'').toLowerCase().includes(t)
        );
    }
    renderInventario(lista);
}

function filtrarStock(tipo, btn) {
    soloStockBajo = !soloStockBajo;
    btn.classList.toggle('btn-primary', soloStockBajo);
    btn.classList.toggle('btn-secondary', !soloStockBajo);
    buscarInventario(document.getElementById('buscar-inv').value);
}

function abrirModalInventario() {
    document.getElementById('modal-inv-titulo').textContent = 'Nueva Refacción';
    document.getElementById('form-inventario').reset();
    document.getElementById('fi-id').value = '';
    document.getElementById('fi-cantidad').value = '0';
    document.getElementById('fi-precio_compra').value = '0';
    document.getElementById('fi-precio_venta').value = '0';
    document.getElementById('fi-stock_minimo').value = '5';
    abrirModal('modal-inventario');
}

function editarInventario(datos) {
    document.getElementById('modal-inv-titulo').textContent = 'Editar Refacción';
    const campos = ['id_refaccion','nombre','descripcion','numero_parte','cantidad','precio_compra','precio_venta','stock_minimo','proveedor','ubicacion'];
    campos.forEach(c => {
        const el = document.getElementById('fi-' + c);
        if (el) el.value = datos[c] ?? '';
    });
    abrirModal('modal-inventario');
}

async function guardarInventario() {
    const f = document.getElementById('form-inventario');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_inventario', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-inventario'); cargarInventario(); }
    else toast(r.mensaje||'Error al guardar', true);
}

function pedirEliminar(id, nombre) {
    idEliminar = id;
    document.getElementById('confirmar-nombre').textContent = '"' + nombre + '"';
    abrirModal('modal-confirmar');
}

async function confirmarEliminar() {
    if (!idEliminar) return;
    const r = await api({accion:'eliminar_inventario', id_refaccion: idEliminar}, 'POST');
    if (r.ok) { toast('Refacción eliminada'); cerrarModal('modal-confirmar'); cargarInventario(); }
    else toast(r.mensaje||'No se pudo eliminar', true);
    idEliminar = null;
}

cargarInventario();
</script>

</main>
</body>
</html>