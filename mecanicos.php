<?php
$paginaActual = 'mecanicos';
$tituloPagina = 'Mecánicos';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Mecánicos</div>
    <div class="topbar-actions">
        <button class="btn btn-primary" onclick="abrirModalMecanico()">+ Nuevo mecanico</button>
    </div>
</div>

<div class="content">
    <div class="filtros" style="margin-bottom:16px">
        <input type="text" id="buscar-mecanico" placeholder="🔍 Buscar por nombre o teléfono…" style="width:300px"
               oninput="cargarMecanicos(this.value)">
    </div>
    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Órd. activas</th>
                        <th>Total órd.</th>
                        <th>Ingresos</th>
                        <th>Salario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-mecanicos">
                    <tr><td colspan="9"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ Modal Confirmar Eliminación Mecánico ══ -->
<div class="overlay" id="modal-confirmar-mecanico">
<div class="modal" style="max-width:400px">
    <div class="modal-header">
        <span class="modal-title" style="color:var(--danger)">Eliminar Mecánico</span>
        <button class="modal-close" onclick="cerrarModal('modal-confirmar-mecanico')">✕</button>
    </div>
    <div class="modal-body">
        <p style="font-size:14px;line-height:1.6">¿Estás seguro de que deseas eliminar a <strong id="confirmar-mecanico-nombre"></strong>? Esta acción no se puede deshacer.</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-confirmar-mecanico')">Cancelar</button>
        <button class="btn btn-danger" onclick="confirmarEliminarMecanico()">🗑️ Eliminar</button>
    </div>
</div>
</div>

<!-- ══ Modal Mecánico ══ -->
<div class="overlay" id="modal-mecanico">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-mecanico-titulo">Nuevo Mecánico</span>
        <button class="modal-close" onclick="cerrarModal('modal-mecanico')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-mecanico" class="form-grid">
            <input type="hidden" name="id_mecanico" id="fm-id">
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input class="form-control" name="nombre" id="fm-nombre" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido paterno *</label>
                <input class="form-control" name="apellido_pat" id="fm-apellido_pat" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido materno</label>
                <input class="form-control" name="apellido_mat" id="fm-apellido_mat">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input class="form-control" name="telefono" id="fm-telefono">
            </div>
            <div class="form-group form-full">
                <label class="form-label">Especialidad</label>
                <input class="form-control" name="especialidad" id="fm-especialidad" placeholder="Ej: Frenos, Motor, Eléctrico…">
            </div>
            <div class="form-group">
                <label class="form-label">Salario ($)</label>
                <input class="form-control" name="salario" id="fm-salario" type="number" min="0" step="0.01" value="0">
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" onclick="cerrarModal('modal-mecanico')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarMecanico()">💾 Guardar</button>
    </div>
</div>
</div>

<script>
let idMecanicoEliminar = null;

async function cargarMecanicos() {
    const d = await api({ accion: 'listar_mecanicos_stats' });
    const tb = document.getElementById('tbody-mecanicos');

    if (!d.ok || !d.datos.length) {
        tb.innerHTML = '<tr><td colspan="9"><div class="empty-state"><div class="icon">🔩</div><p>No hay mecánicos registrados</p></div></td></tr>';
        return;
    }

    tb.innerHTML = d.datos.map(m => `
        <tr>
            <td class="td-mono">${m.id_mecanico}</td>
            <td><strong>${m.nombre} ${m.apellido_pat} ${m.apellido_mat || ''}</strong></td>
            <td class="td-mono">${m.telefono || '—'}</td>
            <td class="td-muted">${m.especialidad || '—'}</td>
            <td class="td-mono" style="color:var(--warning)">${m.ordenes_activas}</td>
            <td class="td-mono">${m.total_ordenes}</td>
            <td class="td-mono">$${Number(m.ingresos_generados).toLocaleString('es-MX')}</td>
            <td class="td-mono">$${Number(m.salario).toLocaleString('es-MX')}</td>
            <td>
                <div style="display:flex;gap:4px">
                    <button class="btn btn-secondary btn-sm" onclick='editarMecanico(${JSON.stringify(m)})'>✏️ Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="pedirEliminarMecanico(${m.id_mecanico},'${(m.nombre+' '+m.apellido_pat).replace(/'/g,"\\'")}')">🗑️</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function abrirModalMecanico(datos=null) {
    document.getElementById('modal-mecanico-titulo').textContent = datos ? 'Editar Mecánico' : 'Nuevo Mecánico';
    const campos = ['nombre','apellido_pat','apellido_mat','telefono','especialidad','salario'];
    campos.forEach(c => {
        const el = document.getElementById('fm-' + c);
        if (el) el.value = datos ? (datos[c] ?? '') : (c === 'salario' ? '0' : '');
    });
    document.getElementById('fm-id').value = datos ? (datos.id_mecanico ?? '') : '';
    abrirModal('modal-mecanico');
}

function editarMecanico(datos) { abrirModalMecanico(datos); }

async function guardarMecanico() {
    const f = document.getElementById('form-mecanico');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_mecanico', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-mecanico'); cargarMecanicos(); }
    else toast(r.mensaje||'Error', true);
}

function pedirEliminarMecanico(id, nombre) {
    idMecanicoEliminar = id;
    document.getElementById('confirmar-mecanico-nombre').textContent = nombre;
    abrirModal('modal-confirmar-mecanico');
}

async function confirmarEliminarMecanico() {
    if (!idMecanicoEliminar) return;
    const r = await api({accion:'eliminar_mecanico', id_mecanico: idMecanicoEliminar}, 'POST');
    if (r.ok) { toast('Mecánico eliminado'); cerrarModal('modal-confirmar-mecanico'); cargarMecanicos(); }
    else toast(r.mensaje||'No se pudo eliminar', true);
    idMecanicoEliminar = null;
}

cargarMecanicos();
</script>

</main>
</body>
</html>
