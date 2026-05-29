<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Taller Mecánico — Sistema de Gestión</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --bg:       #0f0f0f;
    --surface:  #181818;
    --card:     #1f1f1f;
    --border:   #2a2a2a;
    --accent:   #f97316;
    --accent2:  #fb923c;
    --text:     #e5e5e5;
    --muted:    #6b6b6b;
    --success:  #22c55e;
    --warning:  #eab308;
    --danger:   #ef4444;
    --info:     #3b82f6;
    --radius:   8px;
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
}

/* ── SIDEBAR ────────────────────────────────────── */
#sidebar {
    width: 220px;
    min-height: 100vh;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
}

.logo {
    padding: 24px 20px 20px;
    border-bottom: 1px solid var(--border);
}

.logo-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 2px;
    color: var(--accent);
    line-height: 1;
}

.logo-sub {
    font-size: 10px;
    color: var(--muted);
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-top: 4px;
}

nav { padding: 16px 0; flex: 1; }

.nav-section {
    font-size: 9px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--muted);
    padding: 14px 20px 6px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all .15s;
    font-size: 14px;
    font-weight: 500;
    color: #999;
    user-select: none;
}

.nav-item:hover { background: rgba(249,115,22,.08); color: var(--text); }

.nav-item.active {
    border-left-color: var(--accent);
    background: rgba(249,115,22,.12);
    color: var(--accent);
}

.nav-item .icon { font-size: 18px; width: 22px; text-align: center; }

.badge-nav {
    margin-left: auto;
    background: var(--danger);
    color: #fff;
    font-size: 10px;
    border-radius: 10px;
    padding: 1px 6px;
    font-family: 'JetBrains Mono', monospace;
}

/* ── MAIN ────────────────────────────────────────── */
#main {
    margin-left: 220px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.topbar {
    padding: 18px 32px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
    position: sticky;
    top: 0;
    z-index: 50;
}

.topbar-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 26px;
    letter-spacing: 2px;
    color: var(--text);
}

.topbar-actions { display: flex; gap: 10px; align-items: center; }

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--radius);
    border: none;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    transition: all .15s;
    text-decoration: none;
}

.btn-primary { background: var(--accent); color: #fff; }
.btn-primary:hover { background: var(--accent2); }
.btn-secondary { background: var(--border); color: var(--text); }
.btn-secondary:hover { background: #333; }
.btn-danger { background: rgba(239,68,68,.15); color: var(--danger); border: 1px solid rgba(239,68,68,.3); }
.btn-danger:hover { background: rgba(239,68,68,.25); }
.btn-sm { padding: 5px 10px; font-size: 12px; }

.content { padding: 28px 32px; flex: 1; }

/* ── TARJETAS ESTADÍSTICAS ───────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}

.stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    position: relative;
    overflow: hidden;
    transition: border-color .2s;
}

.stat-card:hover { border-color: var(--accent); }

.stat-icon {
    font-size: 28px;
    margin-bottom: 10px;
    display: block;
}

.stat-value {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 36px;
    letter-spacing: 1px;
    line-height: 1;
    color: var(--accent);
}

.stat-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 4px;
}

/* ── PANELES / SECCIONES ─────────────────────────── */
.section { display: none; }
.section.active { display: block; }

.panel {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
}

.panel-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.panel-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 18px;
    letter-spacing: 1px;
    color: var(--text);
}

/* ── TABLA ───────────────────────────────────────── */
.table-wrap { overflow-x: auto; }

table { width: 100%; border-collapse: collapse; font-size: 13px; }

thead th {
    padding: 10px 16px;
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--muted);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

tbody tr {
    border-bottom: 1px solid rgba(42,42,42,.6);
    transition: background .1s;
}

tbody tr:hover { background: rgba(249,115,22,.04); }
tbody tr:last-child { border-bottom: none; }

td { padding: 11px 16px; vertical-align: middle; }

.td-mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    color: var(--accent);
}

.td-muted { color: var(--muted); font-size: 12px; }

/* ── BADGES ──────────────────────────────────────── */
.badge {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    font-family: 'JetBrains Mono', monospace;
}

.badge-recibido    { background: rgba(59,130,246,.15); color: var(--info); }
.badge-en_proceso  { background: rgba(234,179,8,.15);  color: var(--warning); }
.badge-terminado   { background: rgba(249,115,22,.15); color: var(--accent); }
.badge-entregado   { background: rgba(34,197,94,.15);  color: var(--success); }
.badge-cancelado   { background: rgba(239,68,68,.15);  color: var(--danger); }
.badge-pendiente   { background: rgba(107,107,107,.2); color: #aaa; }
.badge-confirmada  { background: rgba(59,130,246,.15); color: var(--info); }
.badge-en_curso    { background: rgba(234,179,8,.15);  color: var(--warning); }
.badge-completada  { background: rgba(34,197,94,.15);  color: var(--success); }

/* ── FILTROS ─────────────────────────────────────── */
.filtros {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.filtros input, .filtros select {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    padding: 7px 12px;
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    outline: none;
    transition: border-color .2s;
}

.filtros input:focus, .filtros select:focus { border-color: var(--accent); }

.filtro-tab {
    padding: 7px 14px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    background: var(--surface);
    transition: all .15s;
}

.filtro-tab:hover { color: var(--text); border-color: #444; }
.filtro-tab.active { background: var(--accent); color: #fff; border-color: var(--accent); }

/* ── MODAL ───────────────────────────────────────── */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.7);
    z-index: 200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(3px);
}

.overlay.show { display: flex; }

.modal {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp .2s ease;
}

@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 1px;
    color: var(--accent);
}

.modal-close {
    background: none;
    border: none;
    color: var(--muted);
    font-size: 22px;
    cursor: pointer;
    line-height: 1;
    transition: color .15s;
}

.modal-close:hover { color: var(--text); }

.modal-body { padding: 24px; }
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* ── FORMULARIO ──────────────────────────────────── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-full { grid-column: 1 / -1; }

.form-group { display: flex; flex-direction: column; gap: 5px; }

.form-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--muted);
    font-weight: 600;
}

.form-control {
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    padding: 9px 12px;
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    outline: none;
    transition: border-color .2s;
    width: 100%;
}

.form-control:focus { border-color: var(--accent); }

.form-control option { background: var(--card); }

/* ── TOAST ───────────────────────────────────────── */
#toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.toast-item {
    background: var(--card);
    border: 1px solid var(--border);
    border-left: 3px solid var(--success);
    padding: 12px 18px;
    border-radius: var(--radius);
    font-size: 13px;
    min-width: 220px;
    animation: fadeIn .2s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}

.toast-item.error { border-left-color: var(--danger); }

@keyframes fadeIn {
    from { transform: translateX(20px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

/* ── EMPTY STATE ─────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
}

.empty-state .icon { font-size: 48px; margin-bottom: 12px; }
.empty-state p { font-size: 14px; }

/* ── AGENDA CITAS ────────────────────────────────── */
.cita-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid var(--accent);
    border-radius: var(--radius);
    padding: 14px 16px;
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: 16px;
    align-items: center;
    margin-bottom: 10px;
    transition: border-color .2s;
}

.cita-card:hover { border-color: var(--accent); }

.cita-hora {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 28px;
    color: var(--accent);
    letter-spacing: 1px;
    line-height: 1;
}

.cita-hora span {
    display: block;
    font-family: 'DM Sans', sans-serif;
    font-size: 10px;
    color: var(--muted);
    letter-spacing: 1px;
}

.cita-info strong { font-size: 14px; font-weight: 600; }
.cita-info p { font-size: 12px; color: var(--muted); margin-top: 2px; }
.cita-info .motivo { font-size: 13px; color: var(--text); margin-top: 4px; }

.cita-actions { display: flex; flex-direction: column; gap: 6px; align-items: flex-end; }

/* ── INDICADOR STOCK ─────────────────────────────── */
.stock-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
}

.stock-fill {
    height: 6px;
    border-radius: 3px;
    background: var(--border);
    flex: 1;
    overflow: hidden;
}

.stock-fill-inner {
    height: 100%;
    border-radius: 3px;
    background: var(--success);
    transition: width .3s;
}

.stock-fill-inner.low   { background: var(--warning); }
.stock-fill-inner.empty { background: var(--danger); }

/* ── LOADING ─────────────────────────────────────── */
.loading {
    text-align: center;
    padding: 40px;
    color: var(--muted);
    font-size: 13px;
}

.spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin .6s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside id="sidebar">
    <div class="logo">
        <div class="logo-title">⚙ Apex Garage </div>
        <div class="logo-sub">Sistema de Gestión</div>
    </div>
    <nav>
        <div class="nav-section">Principal</div>
        <div class="nav-item active" onclick="ir('dashboard')">
            <span class="icon">📊</span> Dashboard
        </div>
        <div class="nav-section">Operaciones</div>
        <div class="nav-item" onclick="ir('citas')">
            <span class="icon">📅</span> Citas
            <span class="badge-nav" id="badge-citas">0</span>
        </div>
        <div class="nav-item" onclick="ir('ordenes')">
            <span class="icon">🔧</span> Órdenes
        </div>
        <div class="nav-section">Catálogos</div>
        <div class="nav-item" onclick="ir('clientes')">
            <span class="icon">👤</span> Clientes
        </div>
        <div class="nav-item" onclick="ir('vehiculos')">
            <span class="icon">🚗</span> Vehículos
        </div>
        <div class="nav-item" onclick="ir('inventario')">
            <span class="icon">📦</span> Inventario
            <span class="badge-nav" id="badge-stock" style="display:none">!</span>
        </div>
    </nav>
</aside>

<!-- ═══════════════════ MAIN ═══════════════════════ -->
<main id="main">
    <div class="topbar">
        <div class="topbar-title" id="topbar-title">Dashboard</div>
        <div class="topbar-actions" id="topbar-actions"></div>
    </div>

    <div class="content">

        <!-- ── DASHBOARD ─────────────────────────── -->
        <section id="sec-dashboard" class="section active">
            <div class="stats-grid" id="stats-grid">
                <div class="loading"><span class="spinner"></span>Cargando...</div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-title">🕐 Citas de Hoy</span>
                        <button class="btn btn-primary btn-sm" onclick="ir('citas')">Ver todas</button>
                    </div>
                    <div id="dash-citas"><div class="loading"><span class="spinner"></span></div></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-title">🔧 Órdenes Activas</span>
                        <button class="btn btn-primary btn-sm" onclick="ir('ordenes')">Ver todas</button>
                    </div>
                    <div id="dash-ordenes"><div class="loading"><span class="spinner"></span></div></div>
                </div>
            </div>
        </section>

        <!-- ── CITAS ──────────────────────────────── -->
        <section id="sec-citas" class="section">
            <div class="filtros" style="margin-bottom:16px">
                <button class="filtro-tab active" onclick="filtrarCitas('proximas',this)">Próximas</button>
                <button class="filtro-tab" onclick="filtrarCitas('hoy',this)">Hoy</button>
                <button class="filtro-tab" onclick="filtrarCitas('semana',this)">Esta semana</button>
                <button class="filtro-tab" onclick="filtrarCitas('todas',this)">Últimos 30 días</button>
            </div>
            <div id="lista-citas"><div class="loading"><span class="spinner"></span>Cargando citas...</div></div>
        </section>

        <!-- ── ÓRDENES ────────────────────────────── -->
        <section id="sec-ordenes" class="section">
            <div class="filtros" style="margin-bottom:16px">
                <button class="filtro-tab active" onclick="filtrarOrdenes('',this)">Activas</button>
                <button class="filtro-tab" onclick="filtrarOrdenes('RECIBIDO',this)">Recibidas</button>
                <button class="filtro-tab" onclick="filtrarOrdenes('EN_PROCESO',this)">En proceso</button>
                <button class="filtro-tab" onclick="filtrarOrdenes('TERMINADO',this)">Terminadas</button>
                <button class="filtro-tab" onclick="filtrarOrdenes('ENTREGADO',this)">Entregadas</button>
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
                                <th>Estatus</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-ordenes">
                            <tr><td colspan="8"><div class="loading"><span class="spinner"></span></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ── CLIENTES ───────────────────────────── -->
        <section id="sec-clientes" class="section">
            <div class="filtros" style="margin-bottom:16px">
                <input type="text" id="buscar-cliente" placeholder="🔍  Buscar por nombre o teléfono…" style="width:280px"
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
                                <th>Teléfono alt.</th>
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
        </section>

        <!-- ── VEHÍCULOS ──────────────────────────── -->
        <section id="sec-vehiculos" class="section">
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
                                <th>KM actual</th>
                                <th>Propietario</th>
                                <th>Teléfono</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-vehiculos">
                            <tr><td colspan="9"><div class="loading"><span class="spinner"></span></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ── INVENTARIO ─────────────────────────── -->
        <section id="sec-inventario" class="section">
            <div class="panel">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>N.° parte</th>
                                <th>Stock</th>
                                <th>Mín.</th>
                                <th>P. compra</th>
                                <th>P. venta</th>
                                <th>Proveedor</th>
                                <th>Ubicación</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-inventario">
                            <tr><td colspan="8"><div class="loading"><span class="spinner"></span></div></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div><!-- /content -->
</main>

<!-- ═══════════════════ MODALES ═══════════════════ -->

<!-- Modal Cliente -->
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
                    <input class="form-control" name="rfc" placeholder="opcional">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modal-cliente')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCliente()">💾 Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Vehículo -->
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
                    <select class="form-control" name="id_cliente" required id="select-cliente-vehiculo">
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
                    <label class="form-label">No. serie / VIN</label>
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

<!-- Modal Cita -->
<div class="overlay" id="modal-cita">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-cita-titulo">Agendar Cita</span>
            <button class="modal-close" onclick="cerrarModal('modal-cita')">✕</button>
        </div>
        <div class="modal-body">
            <form id="form-cita" class="form-grid">
                <input type="hidden" name="id_cita">
                <div class="form-group form-full">
                    <label class="form-label">Cliente *</label>
                    <select class="form-control" name="id_cliente" required id="select-cliente-cita" onchange="cargarVehiculosCliente(this.value,'select-vehiculo-cita')">
                        <option value="">— Seleccionar —</option>
                    </select>
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Vehículo *</label>
                    <select class="form-control" name="id_vehiculo" required id="select-vehiculo-cita">
                        <option value="">— Primero selecciona cliente —</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha *</label>
                    <input class="form-control" name="fecha_cita" type="date" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Hora *</label>
                    <input class="form-control" name="hora_cita" type="time" required>
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Motivo / Problema *</label>
                    <input class="form-control" name="motivo" required placeholder="Describe brevemente el problema o servicio">
                </div>
                <div class="form-group">
                    <label class="form-label">Mecánico asignado</label>
                    <select class="form-control" name="id_mecanico" id="select-mecanico-cita">
                        <option value="">— Sin asignar —</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Estatus</label>
                    <select class="form-control" name="estatus">
                        <option value="PENDIENTE">Pendiente</option>
                        <option value="CONFIRMADA">Confirmada</option>
                    </select>
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Notas adicionales</label>
                    <textarea class="form-control" name="notas" rows="2" style="resize:vertical"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modal-cita')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCita()">💾 Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Orden -->
<div class="overlay" id="modal-orden">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="modal-orden-titulo">Nueva Orden de Servicio</span>
            <button class="modal-close" onclick="cerrarModal('modal-orden')">✕</button>
        </div>
        <div class="modal-body">
            <form id="form-orden" class="form-grid">
                <input type="hidden" name="id_orden">
                <div class="form-group form-full">
                    <label class="form-label">Cliente *</label>
                    <select class="form-control" name="id_cliente_orden" id="select-cliente-orden" onchange="cargarVehiculosCliente(this.value,'select-vehiculo-orden')">
                        <option value="">— Seleccionar —</option>
                    </select>
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Vehículo *</label>
                    <select class="form-control" name="id_vehiculo" required id="select-vehiculo-orden">
                        <option value="">— Primero selecciona cliente —</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Mecánico</label>
                    <select class="form-control" name="id_mecanico" id="select-mecanico-orden">
                        <option value="">— Sin asignar —</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">KM de entrada</label>
                    <input class="form-control" name="km_entrada" type="number" min="0" value="0">
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Descripción / Diagnóstico *</label>
                    <textarea class="form-control" name="descripcion" rows="3" required style="resize:vertical"></textarea>
                </div>
                <div class="form-group form-full">
                    <label class="form-label">Notas internas</label>
                    <textarea class="form-control" name="notas_internas" rows="2" style="resize:vertical" placeholder="Solo visible para el taller"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Estatus inicial</label>
                    <select class="form-control" name="estatus">
                        <option value="RECIBIDO">Recibido</option>
                        <option value="EN_PROCESO">En proceso</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="cerrarModal('modal-orden')">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarOrden()">💾 Guardar</button>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toast"></div>

<!-- ═══════════════════ SCRIPT ═══════════════════ -->
<script>

// ── Estado global ──────────────────────────────────────────
let seccionActual = 'dashboard';
let cacheMecanicos = [];
let cacheClientes  = [];

// ── Navegación ─────────────────────────────────────────────
const titulos = {
    dashboard:  'Dashboard',
    citas:      'Citas Agendadas',
    ordenes:    'Órdenes de Servicio',
    clientes:   'Clientes',
    vehiculos:  'Vehículos',
    inventario: 'Inventario'
};

const acciones = {
    citas:     () => `<button class="btn btn-primary" onclick="abrirModalCita()">+ Nueva cita</button>`,
    ordenes:   () => `<button class="btn btn-primary" onclick="abrirModalOrden()">+ Nueva orden</button>`,
    clientes:  () => `<button class="btn btn-primary" onclick="abrirModalCliente()">+ Nuevo cliente</button>`,
    vehiculos: () => `<button class="btn btn-primary" onclick="abrirModalVehiculo()">+ Nuevo vehículo</button>`,
};

function ir(seccion) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById(`sec-${seccion}`).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => {
        if (n.getAttribute('onclick')?.includes(seccion)) n.classList.add('active');
    });
    document.getElementById('topbar-title').textContent = titulos[seccion] || seccion;
    document.getElementById('topbar-actions').innerHTML = acciones[seccion] ? acciones[seccion]() : '';
    seccionActual = seccion;

    const loaders = {
        dashboard:  cargarDashboard,
        citas:      () => cargarCitas('proximas'),
        ordenes:    () => cargarOrdenes(''),
        clientes:   () => cargarClientes(''),
        vehiculos:  cargarVehiculos,
        inventario: cargarInventario,
    };
    if (loaders[seccion]) loaders[seccion]();
}

// ── API helper ─────────────────────────────────────────────
async function api(params, metodo = 'GET') {
    try {
        let url = 'api.php', opts = {};
        if (metodo === 'GET') {
            url += '?' + new URLSearchParams(params);
        } else {
            opts = { method: 'POST', body: new URLSearchParams(params) };
        }
        const r = await fetch(url, opts);
        return await r.json();
    } catch (e) {
        toast('Error de conexión con el servidor', true);
        return { ok: false };
    }
}

// ── Toast ──────────────────────────────────────────────────
function toast(msg, error = false) {
    const t = document.createElement('div');
    t.className = 'toast-item' + (error ? ' error' : '');
    t.textContent = (error ? '✕ ' : '✓ ') + msg;
    document.getElementById('toast').appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// ── Modales ────────────────────────────────────────────────
function abrirModal(id) { document.getElementById(id).classList.add('show'); }
function cerrarModal(id) { document.getElementById(id).classList.remove('show'); }

// cerrar al click fuera
document.querySelectorAll('.overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) cerrarModal(o.id); });
});

// ── DASHBOARD ─────────────────────────────────────────────
async function cargarDashboard() {
    const d = await api({ accion: 'dashboard' });
    if (!d.ok) return;
    const s = d.datos;

    document.getElementById('stats-grid').innerHTML = `
        <div class="stat-card"><span class="stat-icon">👤</span><div class="stat-value">${s.clientes}</div><div class="stat-label">Clientes</div></div>
        <div class="stat-card"><span class="stat-icon">🚗</span><div class="stat-value">${s.vehiculos}</div><div class="stat-label">Vehículos</div></div>
        <div class="stat-card"><span class="stat-icon">📅</span><div class="stat-value">${s.citas_hoy}</div><div class="stat-label">Citas hoy</div></div>
        <div class="stat-card"><span class="stat-icon">🔧</span><div class="stat-value">${s.ordenes_activas}</div><div class="stat-label">Órdenes activas</div></div>
        <div class="stat-card"><span class="stat-icon">📦</span><div class="stat-value" style="${s.stock_bajo>0?'color:var(--danger)':''}">${s.stock_bajo}</div><div class="stat-label">Stock bajo</div></div>
        <div class="stat-card"><span class="stat-icon">💰</span><div class="stat-value" style="font-size:24px">$${Number(s.ingresos_mes).toLocaleString('es-MX')}</div><div class="stat-label">Ingresos mes</div></div>
    `;

    // badge citas
    document.getElementById('badge-citas').textContent = s.citas_hoy;

    // badge stock
    const bs = document.getElementById('badge-stock');
    if (s.stock_bajo > 0) { bs.style.display = ''; bs.textContent = s.stock_bajo; }

    // mini-listas
    cargarDashCitas();
    cargarDashOrdenes();
}

async function cargarDashCitas() {
    const d = await api({ accion: 'listar_citas', filtro: 'hoy' });
    const el = document.getElementById('dash-citas');
    if (!d.ok || !d.datos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">📅</div><p>No hay citas hoy</p></div>';
        return;
    }
    el.innerHTML = d.datos.slice(0,4).map(c => `
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center">
            <div style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--accent);min-width:55px">${c.hora_cita.substring(0,5)}</div>
            <div style="flex:1">
                <strong style="font-size:13px">${c.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">${c.vehiculo} · ${c.placa}</div>
                <div style="font-size:12px;margin-top:2px">${c.motivo}</div>
            </div>
            <span class="badge badge-${c.estatus_cita?.toLowerCase()}">${c.estatus_cita}</span>
        </div>
    `).join('');
}

async function cargarDashOrdenes() {
    const d = await api({ accion: 'listar_ordenes' });
    const el = document.getElementById('dash-ordenes');
    if (!d.ok || !d.datos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">🔧</div><p>No hay órdenes activas</p></div>';
        return;
    }
    el.innerHTML = d.datos.slice(0,4).map(o => `
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center">
            <div class="td-mono" style="min-width:36px">#${o.id_orden}</div>
            <div style="flex:1">
                <strong style="font-size:13px">${o.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">${o.vehiculo} · ${o.placa}</div>
            </div>
            <span class="badge badge-${o.estatus.toLowerCase()}">${o.estatus.replace('_',' ')}</span>
        </div>
    `).join('');
}

// ── CITAS ──────────────────────────────────────────────────
let filtrosCitaActivo = 'proximas';

async function cargarCitas(filtro) {
    filtrosCitaActivo = filtro;
    const d = await api({ accion: 'listar_citas', filtro });
    const el = document.getElementById('lista-citas');

    if (!d.ok || !d.datos.length) {
        el.innerHTML = '<div class="empty-state"><div class="icon">📅</div><p>No hay citas para mostrar</p></div>';
        return;
    }

    // Agrupar por fecha
    const grupos = {};
    d.datos.forEach(c => {
        if (!grupos[c.fecha_cita]) grupos[c.fecha_cita] = [];
        grupos[c.fecha_cita].push(c);
    });

    let html = '';
    for (const fecha in grupos) {
        const d2 = new Date(fecha + 'T12:00:00');
        const hoy = new Date(); hoy.setHours(0,0,0,0);
        const esHoy = d2.toDateString() === hoy.toDateString();
        html += `<div style="font-family:'Bebas Neue',sans-serif;font-size:15px;letter-spacing:2px;color:var(--muted);margin:16px 0 8px;${esHoy?'color:var(--accent)':''}">${esHoy ? '— HOY ' : ''}${d2.toLocaleDateString('es-MX',{weekday:'long',day:'numeric',month:'long'}).toUpperCase()}</div>`;
        grupos[fecha].forEach(c => {
            html += `
            <div class="cita-card">
                <div class="cita-hora">${c.hora_cita.substring(0,5)}<span>hrs</span></div>
                <div class="cita-info">
                    <strong>${c.cliente}</strong>
                    <p>📞 ${c.telefono} &nbsp;|&nbsp; 🚗 ${c.vehiculo} <span class="td-mono">${c.placa}</span></p>
                    <p class="motivo">${c.motivo}</p>
                    ${c.mecanico ? `<p style="font-size:11px;color:var(--muted);margin-top:2px">🔧 ${c.mecanico}</p>` : ''}
                </div>
                <div class="cita-actions">
                    <span class="badge badge-${c.estatus.toLowerCase()}">${c.estatus}</span>
                    <div style="display:flex;gap:6px;margin-top:6px">
                        <button class="btn btn-secondary btn-sm" onclick="editarCita(${c.id_cita})">✏️</button>
                        <button class="btn btn-danger btn-sm" onclick="cancelarCita(${c.id_cita})">✕</button>
                    </div>
                </div>
            </div>`;
        });
    }
    el.innerHTML = html;
}

function filtrarCitas(filtro, btn) {
    document.querySelectorAll('#sec-citas .filtro-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    cargarCitas(filtro);
}

function abrirModalCita(datos = null) {
    document.getElementById('modal-cita-titulo').textContent = datos ? 'Editar Cita' : 'Agendar Cita';
    const f = document.getElementById('form-cita');
    f.reset();
    if (datos) {
        Object.keys(datos).forEach(k => { if (f.elements[k]) f.elements[k].value = datos[k]; });
    }
    poblarSelectClientes('select-cliente-cita');
    poblarSelectMecanicos('select-mecanico-cita');
    abrirModal('modal-cita');
}

async function editarCita(id) {
    // Para simplificar: re-abre el modal vacío con el id
    // En producción harías un GET de la cita específica
    abrirModalCita();
    document.getElementById('form-cita').elements['id_cita'].value = id;
}

async function guardarCita() {
    const f = document.getElementById('form-cita');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({ accion: 'guardar_cita', ...datos }, 'POST');
    if (r.ok) {
        toast(r.mensaje);
        cerrarModal('modal-cita');
        cargarCitas(filtrosCitaActivo);
    } else {
        toast(r.mensaje || 'Error al guardar', true);
    }
}

async function cancelarCita(id) {
    if (!confirm('¿Cancelar esta cita?')) return;
    const r = await api({ accion: 'cancelar_cita', id_cita: id }, 'POST');
    if (r.ok) { toast('Cita cancelada'); cargarCitas(filtrosCitaActivo); }
}

// ── ÓRDENES ────────────────────────────────────────────────
let filtroOrdenActivo = '';

async function cargarOrdenes(estatus) {
    filtroOrdenActivo = estatus;
    const d = await api({ accion: 'listar_ordenes', estatus });
    const tb = document.getElementById('tbody-ordenes');

    if (!d.ok || !d.datos.length) {
        tb.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="icon">🔧</div><p>No hay órdenes</p></div></td></tr>`;
        return;
    }

    tb.innerHTML = d.datos.map(o => `
        <tr>
            <td class="td-mono">#${o.id_orden}</td>
            <td class="td-muted">${new Date(o.fecha_entrada).toLocaleDateString('es-MX')}</td>
            <td>
                <strong>${o.cliente}</strong>
                <div style="font-size:11px;color:var(--muted)">📞 ${o.telefono}</div>
            </td>
            <td>
                ${o.vehiculo}<br>
                <span class="td-mono">${o.placa}</span>
            </td>
            <td class="td-muted">${o.mecanico || '—'}</td>
            <td><span class="badge badge-${o.estatus.toLowerCase()}">${o.estatus.replace('_',' ')}</span></td>
            <td class="td-mono">${o.total > 0 ? '$' + Number(o.total).toLocaleString('es-MX') : '—'}</td>
            <td>
                <div style="display:flex;gap:4px">
                    <select class="form-control" style="font-size:11px;padding:4px 6px;width:130px" onchange="cambiarEstatusOrden(${o.id_orden},this.value)">
                        <option value="">Cambiar...</option>
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

function filtrarOrdenes(estatus, btn) {
    document.querySelectorAll('#sec-ordenes .filtro-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    cargarOrdenes(estatus);
}

function abrirModalOrden() {
    document.getElementById('modal-orden-titulo').textContent = 'Nueva Orden de Servicio';
    document.getElementById('form-orden').reset();
    poblarSelectClientes('select-cliente-orden');
    poblarSelectMecanicos('select-mecanico-orden');
    abrirModal('modal-orden');
}

async function guardarOrden() {
    const f = document.getElementById('form-orden');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({ accion: 'guardar_orden', ...datos }, 'POST');
    if (r.ok) {
        toast(r.mensaje);
        cerrarModal('modal-orden');
        cargarOrdenes(filtroOrdenActivo);
    } else {
        toast(r.mensaje || 'Error al guardar', true);
    }
}

async function cambiarEstatusOrden(id, estatus) {
    if (!estatus) return;
    const r = await api({ accion: 'cambiar_estatus_orden', id_orden: id, estatus }, 'POST');
    if (r.ok) { toast('Estatus actualizado'); cargarOrdenes(filtroOrdenActivo); }
}

// ── CLIENTES ───────────────────────────────────────────────
async function cargarClientes(q) {
    const d = await api({ accion: 'listar_clientes', q: q || '' });
    const tb = document.getElementById('tbody-clientes');

    if (!d.ok || !d.datos.length) {
        tb.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="icon">👤</div><p>No se encontraron clientes</p></div></td></tr>`;
        return;
    }

    cacheClientes = d.datos;
    tb.innerHTML = d.datos.map(c => `
        <tr>
            <td class="td-mono">${c.id_cliente}</td>
            <td><strong>${c.nombre} ${c.apellido_pat} ${c.apellido_mat || ''}</strong></td>
            <td class="td-mono">${c.telefono}</td>
            <td class="td-mono">${c.telefono_alt || '—'}</td>
            <td class="td-muted">${c.correo || '—'}</td>
            <td class="td-muted" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${c.direccion || '—'}</td>
            <td class="td-muted">${new Date(c.fecha_registro).toLocaleDateString('es-MX')}</td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick='editarCliente(${JSON.stringify(c)})'>✏️</button>
            </td>
        </tr>
    `).join('');
}

function abrirModalCliente(datos = null) {
    document.getElementById('modal-cliente-titulo').textContent = datos ? 'Editar Cliente' : 'Nuevo Cliente';
    const f = document.getElementById('form-cliente');
    f.reset();
    if (datos) {
        Object.keys(datos).forEach(k => { if (f.elements[k]) f.elements[k].value = datos[k] ?? ''; });
    }
    abrirModal('modal-cliente');
}

function editarCliente(datos) { abrirModalCliente(datos); }

async function guardarCliente() {
    const f = document.getElementById('form-cliente');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({ accion: 'guardar_cliente', ...datos }, 'POST');
    if (r.ok) {
        toast(r.mensaje);
        cerrarModal('modal-cliente');
        cargarClientes('');
    } else {
        toast(r.mensaje || 'Error', true);
    }
}

// ── VEHÍCULOS ──────────────────────────────────────────────
async function cargarVehiculos() {
    const d = await api({ accion: 'listar_vehiculos' });
    const tb = document.getElementById('tbody-vehiculos');

    if (!d.ok || !d.datos.length) {
        tb.innerHTML = `<tr><td colspan="9"><div class="empty-state"><div class="icon">🚗</div><p>No hay vehículos registrados</p></div></td></tr>`;
        return;
    }

    tb.innerHTML = d.datos.map(v => `
        <tr>
            <td class="td-mono">${v.placa}</td>
            <td><strong>${v.marca} ${v.modelo}</strong></td>
            <td class="td-muted">${v.anio}</td>
            <td class="td-muted">${v.color || '—'}</td>
            <td class="td-muted">${v.transmision}</td>
            <td class="td-mono">${Number(v.km_actual).toLocaleString('es-MX')} km</td>
            <td><strong>${v.propietario}</strong></td>
            <td class="td-mono">${v.telefono || '—'}</td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick='editarVehiculo(${JSON.stringify(v)})'>✏️</button>
            </td>
        </tr>
    `).join('');
}

function abrirModalVehiculo(datos = null) {
    document.getElementById('modal-vehiculo-titulo').textContent = datos ? 'Editar Vehículo' : 'Nuevo Vehículo';
    const f = document.getElementById('form-vehiculo');
    f.reset();
    poblarSelectClientes('select-cliente-vehiculo');
    if (datos) {
        setTimeout(() => {
            Object.keys(datos).forEach(k => { if (f.elements[k]) f.elements[k].value = datos[k] ?? ''; });
        }, 100);
    }
    abrirModal('modal-vehiculo');
}

function editarVehiculo(datos) { abrirModalVehiculo(datos); }

async function guardarVehiculo() {
    const f = document.getElementById('form-vehiculo');
    const datos = Object.fromEntries(new FormData(f));
    const r = await api({ accion: 'guardar_vehiculo', ...datos }, 'POST');
    if (r.ok) {
        toast(r.mensaje);
        cerrarModal('modal-vehiculo');
        cargarVehiculos();
    } else {
        toast(r.mensaje || 'Error', true);
    }
}

// ── INVENTARIO ─────────────────────────────────────────────
async function cargarInventario() {
    const d = await api({ accion: 'listar_inventario' });
    const tb = document.getElementById('tbody-inventario');

    if (!d.ok) return;

    tb.innerHTML = d.datos.map(i => {
        const pct = Math.min(100, Math.round((i.cantidad / (i.stock_minimo * 3)) * 100));
        const cls = i.cantidad === 0 ? 'empty' : (i.stock_critico == '1' ? 'low' : '');
        return `
        <tr>
            <td>
                <strong>${i.nombre}</strong>
                ${i.descripcion ? `<div class="td-muted" style="font-size:11px">${i.descripcion}</div>` : ''}
                ${i.stock_critico == '1' ? '<span class="badge badge-cancelado" style="margin-top:4px">Stock bajo</span>' : ''}
            </td>
            <td class="td-mono">${i.numero_parte || '—'}</td>
            <td>
                <div class="stock-bar">
                    <span class="td-mono" style="${i.stock_critico=='1'?'color:var(--danger)':''}">${i.cantidad}</span>
                    <div class="stock-fill">
                        <div class="stock-fill-inner ${cls}" style="width:${pct}%"></div>
                    </div>
                </div>
            </td>
            <td class="td-muted">${i.stock_minimo}</td>
            <td class="td-mono">$${Number(i.precio_compra).toLocaleString('es-MX')}</td>
            <td class="td-mono">$${Number(i.precio_venta).toLocaleString('es-MX')}</td>
            <td class="td-muted">${i.proveedor || '—'}</td>
            <td class="td-muted">${i.ubicacion || '—'}</td>
        </tr>`;
    }).join('');
}

// ── Helpers: poblar selects ────────────────────────────────
async function poblarSelectClientes(selectId) {
    if (!cacheClientes.length) {
        const d = await api({ accion: 'listar_clientes', q: '' });
        if (d.ok) cacheClientes = d.datos;
    }
    const sel = document.getElementById(selectId);
    const val = sel.value;
    sel.innerHTML = '<option value="">— Seleccionar cliente —</option>' +
        cacheClientes.map(c => `<option value="${c.id_cliente}">${c.nombre} ${c.apellido_pat} — ${c.telefono}</option>`).join('');
    if (val) sel.value = val;
}

async function poblarSelectMecanicos(selectId) {
    if (!cacheMecanicos.length) {
        const d = await api({ accion: 'listar_mecanicos' });
        if (d.ok) cacheMecanicos = d.datos;
    }
    const sel = document.getElementById(selectId);
    sel.innerHTML = '<option value="">— Sin asignar —</option>' +
        cacheMecanicos.map(m => `<option value="${m.id_mecanico}">${m.nombre} ${m.apellido_pat}</option>`).join('');
}

async function cargarVehiculosCliente(idCliente, selectId) {
    const sel = document.getElementById(selectId);
    if (!idCliente) { sel.innerHTML = '<option value="">— Primero selecciona cliente —</option>'; return; }
    const d = await api({ accion: 'listar_vehiculos', id_cliente: idCliente });
    if (d.ok) {
        sel.innerHTML = d.datos.length
            ? d.datos.map(v => `<option value="${v.id_vehiculo}">${v.marca} ${v.modelo} ${v.anio} — ${v.placa}</option>`).join('')
            : '<option value="">Sin vehículos registrados</option>';
    }
}

// ── Inicio ─────────────────────────────────────────────────
cargarDashboard();
</script>
</body>
</html>
