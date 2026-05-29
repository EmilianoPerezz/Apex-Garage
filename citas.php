<?php
$paginaActual = 'citas';
$tituloPagina = 'Citas';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Citas Agendadas</div>
    <div class="topbar-actions">
        <button class="btn btn-primary" onclick="abrirModalCita()">+ Nueva cita</button>
    </div>
</div>

<div class="content">
    <div class="filtros" style="margin-bottom:16px">
        <button class="filtro-tab active" onclick="filtrarCitas('proximas',this)">Próximas</button>
        <button class="filtro-tab" onclick="filtrarCitas('hoy',this)">Hoy</button>
        <button class="filtro-tab" onclick="filtrarCitas('semana',this)">Esta semana</button>
        <button class="filtro-tab" onclick="filtrarCitas('todas',this)">Últimos 30 días</button>
    </div>
    <div id="lista-citas"><div class="loading"><span class="spinner"></span>Cargando citas...</div></div>
</div>

<!-- ══ Modal Cita ══ -->
<div class="overlay" id="modal-cita">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-cita-titulo">Agendar Cita</span>
        <button class="modal-close" onclick="cerrarModal('modal-cita')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-cita" class="form-grid">
            <input type="hidden" name="id_cita" id="f-id-cita">
            <div class="form-group form-full">
                <label class="form-label">Cliente *</label>
                <select class="form-control" name="id_cliente" id="f-id-cliente" required onchange="cargarVehiculosCliente(this.value,'f-id-vehiculo')">
                    <option value="">— Seleccionar —</option>
                </select>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Vehículo *</label>
                <select class="form-control" name="id_vehiculo" id="f-id-vehiculo" required>
                    <option value="">— Primero selecciona un cliente —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha *</label>
                <input class="form-control" name="fecha_cita" id="f-fecha-cita" type="date" required>
            </div>
            <div class="form-group">
                <label class="form-label">Hora *</label>
                <input class="form-control" name="hora_cita" id="f-hora-cita" type="time" required>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Motivo / Problema *</label>
                <input class="form-control" name="motivo" id="f-motivo" required placeholder="Describe brevemente el problema">
            </div>
            <div class="form-group">
                <label class="form-label">Mecánico asignado</label>
                <select class="form-control" name="id_mecanico" id="f-id-mecanico">
                    <option value="">— Sin asignar —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Estatus</label>
                <select class="form-control" name="estatus" id="f-estatus">
                    <option value="PENDIENTE">Pendiente</option>
                    <option value="CONFIRMADA">Confirmada</option>
                    <option value="EN_CURSO">En curso</option>
                    <option value="COMPLETADA">Completada</option>
                    <option value="CANCELADA">Cancelada</option>
                </select>
            </div>
            <div class="form-group form-full">
                <label class="form-label">Notas adicionales</label>
                <textarea class="form-control" name="notas" id="f-notas" rows="2" style="resize:vertical"></textarea>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-cita')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarCita()">💾 Guardar</button>
    </div>
</div>
</div>

<style>
.cita-card { background:var(--surface); border:1px solid var(--border); border-left:3px solid var(--accent); border-radius:var(--radius); padding:14px 16px; display:grid; grid-template-columns:80px 1fr auto; gap:16px; align-items:center; margin-bottom:10px; transition:border-color .2s; }
.cita-card:hover { border-color:var(--accent2); }
.cita-hora { font-family:'Bebas Neue',sans-serif; font-size:30px; color:var(--accent); letter-spacing:1px; line-height:1; }
.cita-hora span { display:block; font-family:'DM Sans',sans-serif; font-size:10px; color:var(--muted); letter-spacing:1px; margin-top:2px; }
.cita-info strong { font-size:14px; font-weight:600; }
.cita-info p { font-size:12px; color:var(--muted); margin-top:3px; }
.cita-info .motivo { font-size:13px; color:var(--text); margin-top:5px; }
.cita-actions { display:flex; flex-direction:column; gap:6px; align-items:flex-end; }
.grupo-fecha { font-family:'Bebas Neue',sans-serif; font-size:14px; letter-spacing:2px; color:var(--muted); margin:18px 0 8px; padding-bottom:4px; border-bottom:1px solid var(--border); }
.grupo-fecha.hoy { color:var(--accent); }
</style>

<script>
let filtroActivo = 'proximas';
let cacheCitas = [];

async function cargarCitas(filtro) {
    filtroActivo = filtro;
    document.getElementById('lista-citas').innerHTML = '<div class="loading"><span class="spinner"></span>Cargando...</div>';
    const d = await api({accion:'listar_citas', filtro});
    cacheCitas = d.datos || [];
    renderCitas(cacheCitas);
}

function renderCitas(lista) {
    const el = document.getElementById('lista-citas');
    if (!lista.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">📅</div><p>No hay citas para mostrar</p></div>';
        return;
    }
    const grupos = {};
    lista.forEach(c => { if (!grupos[c.fecha_cita]) grupos[c.fecha_cita] = []; grupos[c.fecha_cita].push(c); });
    let html = '';
    const hoyStr = new Date().toISOString().split('T')[0];
    for (const fecha in grupos) {
        const esHoy = fecha === hoyStr;
        const d2 = new Date(fecha + 'T12:00:00');
        html += `<div class="grupo-fecha ${esHoy?'hoy':''}">${esHoy?'— HOY — ':''}${d2.toLocaleDateString('es-MX',{weekday:'long',day:'numeric',month:'long',year:'numeric'}).toUpperCase()}</div>`;
        grupos[fecha].forEach(c => {
            html += `
            <div class="cita-card">
                <div class="cita-hora">${c.hora_cita.substring(0,5)}<span>hrs</span></div>
                <div class="cita-info">
                    <strong>${c.cliente}</strong>
                    <p>📞 ${c.telefono} &nbsp;|&nbsp; 🚗 ${c.vehiculo} — <span style="font-family:'JetBrains Mono',monospace;color:var(--accent)">${c.placa}</span></p>
                    <p class="motivo">${c.motivo}</p>
                    ${c.mecanico ? `<p style="font-size:11px;color:var(--muted);margin-top:3px">🔧 ${c.mecanico}</p>` : ''}
                    ${c.notas ? `<p style="font-size:11px;font-style:italic;color:var(--muted);margin-top:3px">📝 ${c.notas}</p>` : ''}
                </div>
                <div class="cita-actions">
                    <span class="badge badge-${c.estatus.toLowerCase()}">${c.estatus}</span>
                    <div style="display:flex;gap:6px;margin-top:8px">
                        <button class="btn btn-secondary btn-sm" onclick="editarCita(${c.id_cita})">✏️ Editar</button>
                        ${c.estatus!=='CANCELADA'&&c.estatus!=='COMPLETADA'?`<button class="btn btn-danger btn-sm" onclick="cancelarCita(${c.id_cita})">✕</button>`:''}
                    </div>
                </div>
            </div>`;
        });
    }
    el.innerHTML = html;
}

function filtrarCitas(filtro, btn) {
    document.querySelectorAll('.filtro-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    cargarCitas(filtro);
}

// Abrir modal vacío (nueva cita)
async function abrirModalCita() {
    document.getElementById('modal-cita-titulo').textContent = 'Agendar Cita';
    document.getElementById('form-cita').reset();
    document.getElementById('f-id-cita').value = '';
    await poblarSelectClientes();
    await poblarSelectMecanicos();
    abrirModal('modal-cita');
}

// Editar: carga todos los datos de la cita en el modal
async function editarCita(id) {
    const d = await api({accion:'obtener_cita', id_cita: id});
    if (!d.ok) { toast('No se pudo cargar la cita', true); return; }
    const c = d.datos;
    document.getElementById('modal-cita-titulo').textContent = 'Editar Cita #' + id;
    await poblarSelectClientes();
    await poblarSelectMecanicos();
    // Setear valores uno a uno para manejar el select encadenado de vehículos
    document.getElementById('f-id-cita').value    = c.id_cita;
    document.getElementById('f-id-cliente').value  = c.id_cliente;
    document.getElementById('f-fecha-cita').value  = c.fecha_cita;
    document.getElementById('f-hora-cita').value   = c.hora_cita.substring(0,5);
    document.getElementById('f-motivo').value      = c.motivo;
    document.getElementById('f-notas').value       = c.notas || '';
    document.getElementById('f-estatus').value     = c.estatus;
    // Cargar vehículos del cliente y luego seleccionar el correcto
    await cargarVehiculosCliente(c.id_cliente, 'f-id-vehiculo');
    document.getElementById('f-id-vehiculo').value = c.id_vehiculo;
    if (c.id_mecanico) document.getElementById('f-id-mecanico').value = c.id_mecanico;
    abrirModal('modal-cita');
}

async function guardarCita() {
    const f = document.getElementById('form-cita');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_cita', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-cita'); cargarCitas(filtroActivo); }
    else toast(r.mensaje || 'Error al guardar', true);
}

async function cancelarCita(id) {
    if (!confirm('¿Cancelar esta cita?')) return;
    const r = await api({accion:'cancelar_cita', id_cita:id}, 'POST');
    if (r.ok) { toast('Cita cancelada'); cargarCitas(filtroActivo); }
}

// Selects helpers
let cacheClientes = [], cacheMecanicos = [];

async function poblarSelectClientes() {
    if (!cacheClientes.length) {
        const d = await api({accion:'listar_clientes', q:''});
        if (d.ok) cacheClientes = d.datos;
    }
    const sel = document.getElementById('f-id-cliente');
    sel.innerHTML = '<option value="">— Seleccionar cliente —</option>' +
        cacheClientes.map(c=>`<option value="${c.id_cliente}">${c.nombre} ${c.apellido_pat} — ${c.telefono}</option>`).join('');
}

async function poblarSelectMecanicos() {
    if (!cacheMecanicos.length) {
        const d = await api({accion:'listar_mecanicos'});
        if (d.ok) cacheMecanicos = d.datos;
    }
    const sel = document.getElementById('f-id-mecanico');
    sel.innerHTML = '<option value="">— Sin asignar —</option>' +
        cacheMecanicos.map(m=>`<option value="${m.id_mecanico}">${m.nombre} ${m.apellido_pat}</option>`).join('');
}

async function cargarVehiculosCliente(idCliente, selectId) {
    const sel = document.getElementById(selectId);
    if (!idCliente) { sel.innerHTML = '<option value="">— Primero selecciona un cliente —</option>'; return; }
    const d = await api({accion:'listar_vehiculos', id_cliente:idCliente});
    sel.innerHTML = d.datos?.length
        ? d.datos.map(v=>`<option value="${v.id_vehiculo}">${v.marca} ${v.modelo} ${v.anio} — ${v.placa}</option>`).join('')
        : '<option value="">Sin vehículos registrados</option>';
}

cargarCitas('proximas');
</script>

</main>
</body>
</html>
