<?php
$paginaActual = 'dashboard';
$tituloPagina = 'Dashboard';
include("layout.php");
?>

<div class="topbar">
    <div class="topbar-title">Dashboard</div>
    <div class="topbar-actions">
        <span style="font-size:12px;color:var(--muted)" id="fecha-hoy"></span>
    </div>
</div>

<div class="content">

    <div class="stats-grid" id="stats-grid">
        <div class="loading"><span class="spinner"></span>Cargando...</div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">📅 Citas de Hoy</span>
                <a href="citas.php" class="btn btn-primary btn-sm">Ver todas</a>
            </div>
            <div id="dash-citas"><div class="loading"><span class="spinner"></span></div></div>
        </div>
        <div class="panel">
            <div class="panel-header">
                <span class="panel-title">🔧 Órdenes Activas</span>
                <a href="ordenes.php" class="btn btn-primary btn-sm">Ver todas</a>
            </div>
            <div id="dash-ordenes"><div class="loading"><span class="spinner"></span></div></div>
        </div>
    </div>

    <div style="margin-top:20px" class="panel">
        <div class="panel-header">
            <span class="panel-title">⚠️ Refacciones con Stock Bajo</span>
            <a href="inventario.php" class="btn btn-secondary btn-sm">Ver inventario</a>
        </div>
        <div id="dash-stock"><div class="loading"><span class="spinner"></span></div></div>
    </div>

</div>

<script>
document.getElementById('fecha-hoy').textContent = new Date().toLocaleDateString('es-MX',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

async function cargarDashboard() {
    const d = await api({accion:'dashboard'});
    if (!d.ok) return;
    const s = d.datos;
    document.getElementById('stats-grid').innerHTML = `
        <div class="stat-card"><span class="stat-icon">👤</span><div class="stat-value">${s.clientes}</div><div class="stat-label">Clientes activos</div></div>
        <div class="stat-card"><span class="stat-icon">🚗</span><div class="stat-value">${s.vehiculos}</div><div class="stat-label">Vehículos</div></div>
        <div class="stat-card"><span class="stat-icon">📅</span><div class="stat-value">${s.citas_hoy}</div><div class="stat-label">Citas hoy</div></div>
        <div class="stat-card"><span class="stat-icon">🔧</span><div class="stat-value">${s.ordenes_activas}</div><div class="stat-label">Órdenes activas</div></div>
        <div class="stat-card"><span class="stat-icon">🔩</span><div class="stat-value">${s.mecanicos}</div><div class="stat-label">Mecánicos activos</div></div>
        <div class="stat-card"><span class="stat-icon">📦</span><div class="stat-value" style="${s.stock_bajo>0?'color:var(--danger)':''}">${s.stock_bajo}</div><div class="stat-label">Stock bajo</div></div>
        <div class="stat-card"><span class="stat-icon">💰</span><div class="stat-value" style="font-size:22px">$${Number(s.ingresos_mes).toLocaleString('es-MX')}</div><div class="stat-label">Ingresos del mes</div></div>
    `;
}

async function cargarDashCitas() {
    const d = await api({accion:'listar_citas', filtro:'hoy'});
    const el = document.getElementById('dash-citas');
    if (!d.ok || !d.datos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">📅</div><p>No hay citas hoy</p></div>';
        return;
    }
    el.innerHTML = d.datos.map(c => `
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--accent);min-width:58px">${c.hora_cita.substring(0,5)}</div>
            <div style="flex:1">
                <strong style="font-size:13px">${c.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">${c.vehiculo} · <span style="font-family:'JetBrains Mono',monospace">${c.placa}</span></div>
                <div style="font-size:12px;margin-top:2px">${c.motivo}</div>
            </div>
            <span class="badge badge-${c.estatus.toLowerCase()}">${c.estatus}</span>
        </div>
    `).join('');
}

async function cargarDashOrdenes() {
    const d = await api({accion:'listar_ordenes', estatus:''});
    const el = document.getElementById('dash-ordenes');
    if (!d.ok || !d.datos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">🔧</div><p>No hay órdenes activas</p></div>';
        return;
    }
    el.innerHTML = d.datos.slice(0,5).map(o => `
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center">
            <div style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--accent);min-width:36px">#${o.id_orden}</div>
            <div style="flex:1">
                <strong style="font-size:13px">${o.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">${o.vehiculo} · <span style="font-family:'JetBrains Mono',monospace">${o.placa}</span></div>
            </div>
            <span class="badge badge-${o.estatus.toLowerCase()}">${o.estatus.replace('_',' ')}</span>
        </div>
    `).join('');
}

async function cargarDashStock() {
    const d = await api({accion:'listar_inventario'});
    const el = document.getElementById('dash-stock');
    const bajos = d.datos?.filter(i => i.stock_critico == '1') || [];
    if (!bajos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">✅</div><p>Todo el inventario está en niveles adecuados</p></div>';
        return;
    }
    el.innerHTML = `<table><thead><tr><th>Refacción</th><th>Stock actual</th><th>Mínimo</th><th>Proveedor</th></tr></thead><tbody>` +
        bajos.map(i => `
            <tr>
                <td><strong>${i.nombre}</strong></td>
                <td><span style="color:var(--danger);font-family:'JetBrains Mono',monospace;font-weight:700">${i.cantidad}</span></td>
                <td class="td-muted">${i.stock_minimo}</td>
                <td class="td-muted">${i.proveedor || '—'}</td>
            </tr>
        `).join('') + `</tbody></table>`;
}

cargarDashboard();
cargarDashCitas();
cargarDashOrdenes();
cargarDashStock();
</script>

</main>
</body>
</html>
