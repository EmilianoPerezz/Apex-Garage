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
                        <th>Tel. alternativo</th>
                        <th>Correo</th>
                        <th>Dirección</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-mecanicos">
                    <tr><td colspan="8"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
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
            <input type="hidden" name="id_mecanico">
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input class="form-control" name="nombre" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido paterno *</label>
                <input class="form-control" name="apellido_pat" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido materno</label>
                <input class="form-control" name="apellido_mat">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono *</label>
                <input class="form-control" name="telefono" required>
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono alternativo</label>
                <input class="form-control" name="telefono_alt">
            </div>
            <div class="form-group">
                <label class="form-label">Correo electrónico</label>
                <input class="form-control" name="correo" type="email">
            </div>
            <div class="form-group form-full">
                <label class="form-label">Dirección</label>
                <input class="form-control" name="direccion">
            </div>
            <div class="form-group">
                <label class="form-label">RFC</label>
                <input class="form-control" name="rfc" placeholder="Opcional">
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
async function cargarMecanicos() {
    const d = await api({ accion: 'listar_mecanicos_stats' });
    const tb = document.getElementById('tbody-mecanicos');

    if (!d.ok || !d.datos.length) {
        tb.innerHTML = '<tr><td colspan="8"><div class="empty-state"><div class="icon">🔩</div><p>No hay mecánicos registrados</p></div></td></tr>';
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
                <button class="btn btn-secondary btn-sm" onclick='editarMecanico(${JSON.stringify(m)})'>✏️ Editar</button>
            </td>
        </tr>
    `).join('');
}

function abrirModalMecanico(datos=null) {
    document.getElementById('modal-mecanico-titulo').textContent = datos ? 'Editar Mecánico' : 'Nuevo Mecánico';
    const f = document.getElementById('form-mecanico');
    f.reset();
    if (datos) Object.keys(datos).forEach(k => { if (f.elements[k]) f.elements[k].value = datos[k]??''; });
    abrirModal('modal-mecanico');
}

function editarMecanico(datos) { abrirModalMecanico(datos); }

async function guardarMecanico() {
    const f = document.getElementById('form-mecanico');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_mecanico', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-mecanico'); cargarMecanicos(''); }
    else toast(r.mensaje||'Error', true);
}

cargarMecanicos('');
</script>

</main>
</body>
</html>
