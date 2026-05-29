<?php
$paginaActual = 'ordenes';
$tituloPagina = 'Órdenes de Servicio';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Órdenes de Servicio</div>
    <div class="topbar-actions">
        <button class="btn btn-primary" onclick="abrirModalOrden()">+ Nueva orden</button>
    </div>
</div>

<div class="content">
    <div class="filtros" style="margin-bottom:16px">
        <button class="filtro-tab active" onclick="filtrarOrdenes('ACTIVAS',this)">Activas</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('RECIBIDO',this)">Recibidas</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('EN_PROCESO',this)">En proceso</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('TERMINADO',this)">Terminadas</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('ENTREGADO',this)">Entregadas</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('CANCELADO',this)">Canceladas</button>
        <button class="filtro-tab" onclick="filtrarOrdenes('TODAS',this)">Todas</button>
        <input type="text" id="buscar-orden" placeholder="🔍 Buscar cliente o placa…" style="margin-left:auto;width:220px" oninput="buscarOrden(this.value)">
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha entrada</th>
                        <th>Cliente</th>
                        <th>Vehículo / Placa</th>
                        <th>Mecánico</th>
                        <th>KM entrada</th>
                        <th>Estatus</th>
                        <th>Total</th>
                        <th>Pagado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-ordenes">
                    <tr><td colspan="10"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ Modal Orden ══ -->
<div class="overlay" id="modal-orden">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-orden-titulo">Nueva Orden de Servicio</span>
        <button class="modal-close" onclick="cerrarModal('modal-orden')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-orden" class="form-grid">
            <input type="hidden" name="id_orden" id="fo-id-orden">
            <div class="form-group form-full">
                <label class="form-label">Cliente *</label>
                <select class="form-control" name="id_cliente_orden" id="fo-id-cliente" onchange="cargarVehiculosCliente(this.value,'fo-id-vehiculo')">
                    <option value="">— Seleccionar —</option>
                </select>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Vehículo *</label>
                <select class="form-control" name="id_vehiculo" id="fo-id-vehiculo" required>
                    <option value="">— Primero selecciona un cliente —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Mecánico</label>
                <select class="form-control" name="id_mecanico" id="fo-id-mecanico">
                    <option value="">— Sin asignar —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">KM de entrada</label>
                <input class="form-control" name="km_entrada" id="fo-km" type="number" min="0" value="0">
            </div>
            <div class="form-group form-full">
                <label class="form-label">Descripción / Diagnóstico *</label>
                <textarea class="form-control" name="descripcion" id="fo-descripcion" rows="3" required style="resize:vertical"></textarea>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Notas internas</label>
                <textarea class="form-control" name="notas_internas" id="fo-notas" rows="2" style="resize:vertical" placeholder="Solo visible para el taller"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Estatus</label>
                <select class="form-control" name="estatus" id="fo-estatus">
                    <option value="RECIBIDO">Recibido</option>
                    <option value="EN_PROCESO">En proceso</option>
                    <option value="TERMINADO">Terminado</option>
                    <option value="ENTREGADO">Entregado</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Total ($)</label>
                <input class="form-control" name="total" id="fo-total" type="number" min="0" step="0.01" value="0">
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-orden')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarOrden()">💾 Guardar</button>
    </div>
</div>
</div>

<script>
let filtroOrden = 'ACTIVAS';
let todasOrdenes = [];
let cacheClientes = [], cacheMecanicos = [];

async function cargarOrdenes(filtro) {
    filtroOrden = filtro;
    document.getElementById('tbody-ordenes').innerHTML =
        '<tr><td colspan="10"><div class="loading"><span class="spinner"></span></div></td></tr>';
    const param = (filtro === 'ACTIVAS' || filtro === 'TODAS') ? '' : filtro;
    const todas  = filtro === 'TODAS';
    const d = await api({accion:'listar_ordenes', estatus: param, todas: todas ? '1' : '0'});
    todasOrdenes = d.datos || [];
    renderOrdenes(todasOrdenes);
}

function renderOrdenes(lista) {
    const tb = document.getElementById('tbody-ordenes');
    if (!lista.length) {
        tb.innerHTML = '<tr><td colspan="10"><div class="empty-state"><div class="icon">🔧</div><p>No hay órdenes</p></div></td></tr>';
        return;
    }
    tb.innerHTML = lista.map(o => `
        <tr>
            <td class="td-mono">#${o.id_orden}</td>
            <td class="td-muted">${new Date(o.fecha_entrada).toLocaleDateString('es-MX')}</td>
            <td>
                <strong>${o.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">📞 ${o.telefono}</div>
            </td>
            <td>
                <span>${o.vehiculo}</span><br>
                <span class="td-mono">${o.placa}</span>
            </td>
            <td class="td-muted">${o.mecanico || '—'}</td>
            <td class="td-mono">${o.km_entrada ? Number(o.km_entrada).toLocaleString('es-MX') : '—'}</td>
            <td><span class="badge badge-${o.estatus.toLowerCase()}">${o.estatus.replace('_',' ')}</span></td>
            <td class="td-mono">${o.total > 0 ? '$'+Number(o.total).toLocaleString('es-MX') : '—'}</td>
            <td>
                ${o.pagado == '1'
                    ? '<span class="badge badge-entregado">✓ Pagado</span>'
                    : '<span class="badge badge-cancelado">Pendiente</span>'}
            </td>
            <td>
                <div style="display:flex;gap:4px;flex-wrap:wrap">
                    <button class="btn btn-secondary btn-sm" onclick="editarOrden(${o.id_orden})">✏️</button>
                    <select class="form-control" style="font-size:11px;padding:4px 6px;width:120px"
                            onchange="cambiarEstatus(${o.id_orden},this.value,this)">
                        <option value="">Estatus...</option>
                        <option value="RECIBIDO">Recibido</option>
                        <option value="EN_PROCESO">En proceso</option>
                        <option value="TERMINADO">Terminado</option>
                        <option value="ENTREGADO">Entregado</option>
                        <option value="CANCELADO">Cancelado</option>
                    </select>
                </div>
            </td>
        </tr>
    `).join('');
}

function buscarOrden(q) {
    if (!q) { renderOrdenes(todasOrdenes); return; }
    const t = q.toLowerCase();
    renderOrdenes(todasOrdenes.filter(o =>
        o.cliente.toLowerCase().includes(t) || o.placa.toLowerCase().includes(t)
    ));
}

function filtrarOrdenes(filtro, btn) {
    document.querySelectorAll('.filtro-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    cargarOrdenes(filtro);
}

async function abrirModalOrden() {
    document.getElementById('modal-orden-titulo').textContent = 'Nueva Orden de Servicio';
    document.getElementById('form-orden').reset();
    document.getElementById('fo-id-orden').value = '';
    await poblarSelectClientes();
    await poblarSelectMecanicos();
    abrirModal('modal-orden');
}

async function editarOrden(id) {
    const d = await api({accion:'obtener_orden', id_orden: id});
    if (!d.ok) { toast('No se pudo cargar la orden', true); return; }
    const o = d.datos;
    document.getElementById('modal-orden-titulo').textContent = 'Editar Orden #' + id;
    await poblarSelectClientes();
    await poblarSelectMecanicos();
    document.getElementById('fo-id-orden').value   = o.id_orden;
    document.getElementById('fo-id-cliente').value  = o.id_cliente;
    await cargarVehiculosCliente(o.id_cliente, 'fo-id-vehiculo');
    document.getElementById('fo-id-vehiculo').value = o.id_vehiculo;
    if (o.id_mecanico) document.getElementById('fo-id-mecanico').value = o.id_mecanico;
    document.getElementById('fo-km').value          = o.km_entrada || 0;
    document.getElementById('fo-descripcion').value = o.descripcion;
    document.getElementById('fo-notas').value       = o.notas_internas || '';
    document.getElementById('fo-estatus').value     = o.estatus;
    document.getElementById('fo-total').value       = o.total || 0;
    abrirModal('modal-orden');
}

async function guardarOrden() {
    const f = document.getElementById('form-orden');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_orden', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-orden'); cargarOrdenes(filtroOrden); }
    else toast(r.mensaje || 'Error al guardar', true);
}

async function cambiarEstatus(id, estatus, sel) {
    if (!estatus) return;
    const r = await api({accion:'cambiar_estatus_orden', id_orden:id, estatus}, 'POST');
    if (r.ok) { toast('Estatus actualizado'); cargarOrdenes(filtroOrden); }
    else { sel.value = ''; toast('Error al actualizar', true); }
}

// Helpers select
async function poblarSelectClientes() {
    if (!cacheClientes.length) {
        const d = await api({accion:'listar_clientes', q:''});
        if (d.ok) cacheClientes = d.datos;
    }
    document.getElementById('fo-id-cliente').innerHTML =
        '<option value="">— Seleccionar cliente —</option>' +
        cacheClientes.map(c=>`<option value="${c.id_cliente}">${c.nombre} ${c.apellido_pat} — ${c.telefono}</option>`).join('');
}

async function poblarSelectMecanicos() {
    if (!cacheMecanicos.length) {
        const d = await api({accion:'listar_mecanicos'});
        if (d.ok) cacheMecanicos = d.datos;
    }
    document.getElementById('fo-id-mecanico').innerHTML =
        '<option value="">— Sin asignar —</option>' +
        cacheMecanicos.map(m=>`<option value="${m.id_mecanico}">${m.nombre} ${m.apellido_pat}</option>`).join('');
}

async function cargarVehiculosCliente(idCliente, selectId) {
    const sel = document.getElementById(selectId);
    if (!idCliente) { sel.innerHTML = '<option value="">— Primero selecciona cliente —</option>'; return; }
    const d = await api({accion:'listar_vehiculos', id_cliente:idCliente});
    sel.innerHTML = d.datos?.length
        ? d.datos.map(v=>`<option value="${v.id_vehiculo}">${v.marca} ${v.modelo} ${v.anio} — ${v.placa}</option>`).join('')
        : '<option value="">Sin vehículos registrados</option>';
}

cargarOrdenes('ACTIVAS');
</script>

</main>
</body>
</html>
