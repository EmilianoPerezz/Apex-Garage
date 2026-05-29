<?php
$paginaActual = 'vehiculos';
$tituloPagina = 'Vehículos';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Vehículos</div>
    <div class="topbar-actions">
        <button class="btn btn-primary" onclick="abrirModalVehiculo()">+ Nuevo vehículo</button>
    </div>
</div>

<div class="content">
    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Vehículo</th>
                        <th>Año</th>
                        <th>Color</th>
                        <th>Transmisión</th>
                        <th>Combustible</th>
                        <th>KM actual</th>
                        <th>Propietario</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-vehiculos">
                    <tr><td colspan="10"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ Modal Vehículo ══ -->
<div class="overlay" id="modal-vehiculo">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-vehiculo-titulo">Nuevo Vehículo</span>
        <button class="modal-close" onclick="cerrarModal('modal-vehiculo')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-vehiculo" class="form-grid">
            <input type="hidden" name="id_vehiculo">
            <div class="form-group form-full">
                <label class="form-label">Propietario *</label>
                <select class="form-control" name="id_cliente" id="fv-id-cliente" required>
                    <option value="">— Seleccionar cliente —</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Marca *</label>
                <input class="form-control" name="marca" required>
            </div>
            <div class="form-group">
                <label class="form-label">Modelo *</label>
                <input class="form-control" name="modelo" required>
            </div>
            <div class="form-group">
                <label class="form-label">Año *</label>
                <input class="form-control" name="anio" type="number" min="1990" max="2030" required>
            </div>
            <div class="form-group">
                <label class="form-label">Color</label>
                <input class="form-control" name="color">
            </div>
            <div class="form-group">
                <label class="form-label">Placa *</label>
                <input class="form-control" name="placa" required style="text-transform:uppercase">
            </div>
            <div class="form-group">
                <label class="form-label">KM actual</label>
                <input class="form-control" name="km_actual" type="number" min="0" value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Transmisión</label>
                <select class="form-control" name="transmision">
                    <option value="MANUAL">Manual</option>
                    <option value="AUTOMATICA">Automática</option>
                    <option value="CVT">CVT</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Combustible</label>
                <select class="form-control" name="combustible">
                    <option value="GASOLINA">Gasolina</option>
                    <option value="DIESEL">Diesel</option>
                    <option value="HIBRIDO">Híbrido</option>
                    <option value="ELECTRICO">Eléctrico</option>
                </select>
            </div>
            <div class="form-group form-full">
                <label class="form-label">No. Serie / VIN</label>
                <input class="form-control" name="numero_serie">
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-vehiculo')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarVehiculo()">💾 Guardar</button>
    </div>
</div>
</div>

<script>
let cacheClientes = [];

async function cargarVehiculos() {
    const d = await api({accion:'listar_vehiculos'});
    const tb = document.getElementById('tbody-vehiculos');
    if (!d.ok || !d.datos.length) {
        tb.innerHTML = '<tr><td colspan="10"><div class="empty-state"><div class="icon">🚗</div><p>No hay vehículos registrados</p></div></td></tr>';
        return;
    }
    tb.innerHTML = d.datos.map(v => `
        <tr>
            <td class="td-mono">${v.placa}</td>
            <td><strong>${v.marca} ${v.modelo}</strong></td>
            <td class="td-muted">${v.anio}</td>
            <td class="td-muted">${v.color||'—'}</td>
            <td class="td-muted">${v.transmision}</td>
            <td class="td-muted">${v.combustible}</td>
            <td class="td-mono">${Number(v.km_actual).toLocaleString('es-MX')} km</td>
            <td><strong>${v.propietario}</strong></td>
            <td class="td-mono">${v.telefono||'—'}</td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick='editarVehiculo(${JSON.stringify(v)})'>✏️ Editar</button>
            </td>
        </tr>
    `).join('');
}

async function abrirModalVehiculo(datos=null) {
    document.getElementById('modal-vehiculo-titulo').textContent = datos ? 'Editar Vehículo' : 'Nuevo Vehículo';
    const f = document.getElementById('form-vehiculo');
    f.reset();
    await poblarSelectClientes();
    if (datos) {
        Object.keys(datos).forEach(k => { if(f.elements[k]) f.elements[k].value = datos[k]??''; });
    }
    abrirModal('modal-vehiculo');
}

function editarVehiculo(datos) { abrirModalVehiculo(datos); }

async function guardarVehiculo() {
    const f = document.getElementById('form-vehiculo');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_vehiculo', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-vehiculo'); cargarVehiculos(); }
    else toast(r.mensaje||'Error', true);
}

async function poblarSelectClientes() {
    if (!cacheClientes.length) {
        const d = await api({accion:'listar_clientes', q:''});
        if (d.ok) cacheClientes = d.datos;
    }
    const sel = document.getElementById('fv-id-cliente');
    const val = sel.value;
    sel.innerHTML = '<option value="">— Seleccionar cliente —</option>' +
        cacheClientes.map(c=>`<option value="${c.id_cliente}">${c.nombre} ${c.apellido_pat} — ${c.telefono}</option>`).join('');
    if (val) sel.value = val;
}

cargarVehiculos();
</script>

</main>
</body>
</html>
