<?php
$paginaActual = 'clientes';
$tituloPagina = 'Clientes';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Clientes</div>
    <div class="topbar-actions">
        <button class="btn btn-primary" onclick="abrirModalCliente()">+ Nuevo cliente</button>
    </div>
</div>

<div class="content">
    <div class="filtros" style="margin-bottom:16px">
        <input type="text" id="buscar-cliente" placeholder="🔍 Buscar por nombre o teléfono…" style="width:300px"
               oninput="cargarClientes(this.value)">
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
                <tbody id="tbody-clientes">
                    <tr><td colspan="8"><div class="loading"><span class="spinner"></span></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ Modal Cliente ══ -->
<div class="overlay" id="modal-cliente">
<div class="modal">
    <div class="modal-header">
        <span class="modal-title" id="modal-cliente-titulo">Nuevo Cliente</span>
        <button class="modal-close" onclick="cerrarModal('modal-cliente')">✕</button>
    </div>
    <div class="modal-body">
        <form id="form-cliente" class="form-grid">
            <input type="hidden" name="id_cliente">
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
        <button class="btn btn-secondary" onclick="cerrarModal('modal-cliente')">Cancelar</button>
        <button class="btn btn-primary" onclick="guardarCliente()">💾 Guardar</button>
    </div>
</div>
</div>

<script>
async function cargarClientes(q) {
    const d = await api({accion:'listar_clientes', q: q||''});
    const tb = document.getElementById('tbody-clientes');
    if (!d.ok || !d.datos.length) {
        tb.innerHTML = '<tr><td colspan="8"><div class="empty-state"><div class="icon">👤</div><p>No se encontraron clientes</p></div></td></tr>';
        return;
    }
    tb.innerHTML = d.datos.map(c => `
        <tr>
            <td class="td-mono">${c.id_cliente}</td>
            <td><strong>${c.nombre} ${c.apellido_pat} ${c.apellido_mat||''}</strong></td>
            <td class="td-mono">${c.telefono}</td>
            <td class="td-mono">${c.telefono_alt||'—'}</td>
            <td class="td-muted">${c.correo||'—'}</td>
            <td class="td-muted" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.direccion||'—'}</td>
            <td class="td-muted">${new Date(c.fecha_registro).toLocaleDateString('es-MX')}</td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick='editarCliente(${JSON.stringify(c)})'>✏️ Editar</button>
            </td>
        </tr>
    `).join('');
}

function abrirModalCliente(datos=null) {
    document.getElementById('modal-cliente-titulo').textContent = datos ? 'Editar Cliente' : 'Nuevo Cliente';
    const f = document.getElementById('form-cliente');
    f.reset();
    if (datos) Object.keys(datos).forEach(k => { if (f.elements[k]) f.elements[k].value = datos[k]??''; });
    abrirModal('modal-cliente');
}

function editarCliente(datos) { abrirModalCliente(datos); }

async function guardarCliente() {
    const f = document.getElementById('form-cliente');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({accion:'guardar_cliente', ...datos}, 'POST');
    if (r.ok) { toast(r.mensaje); cerrarModal('modal-cliente'); cargarClientes(''); }
    else toast(r.mensaje||'Error', true);
}

cargarClientes('');
</script>

</main>
</body>
</html>
