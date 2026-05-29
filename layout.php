<?php
// layout.php — Incluir al inicio de cada página
// Uso: include("layout.php"); con $paginaActual y $tituloPagina definidos antes
$paginaActual = $paginaActual ?? 'dashboard';
$tituloPagina = $tituloPagina ?? 'Apex Garage';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apex Garage — <?= $tituloPagina ?></title>
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
body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

/* SIDEBAR */
#sidebar { width:220px; min-height:100vh; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0; left:0; bottom:0; z-index:100; }
.logo { padding:24px 20px 20px; border-bottom:1px solid var(--border); }
.logo-title { font-family:'Bebas Neue',sans-serif; font-size:24px; letter-spacing:3px; color:var(--accent); line-height:1; }
.logo-sub { font-size:10px; color:var(--muted); letter-spacing:3px; text-transform:uppercase; margin-top:4px; }
nav { padding:16px 0; flex:1; }
.nav-section { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--muted); padding:14px 20px 6px; }
.nav-item { display:flex; align-items:center; gap:10px; padding:10px 20px; cursor:pointer; border-left:3px solid transparent; transition:all .15s; font-size:14px; font-weight:500; color:#999; text-decoration:none; }
.nav-item:hover { background:rgba(249,115,22,.08); color:var(--text); }
.nav-item.active { border-left-color:var(--accent); background:rgba(249,115,22,.12); color:var(--accent); }
.nav-item .icon { font-size:18px; width:22px; text-align:center; }
.badge-nav { margin-left:auto; background:var(--danger); color:#fff; font-size:10px; border-radius:10px; padding:1px 6px; font-family:'JetBrains Mono',monospace; }

/* MAIN */
#main { margin-left:220px; flex:1; display:flex; flex-direction:column; min-height:100vh; }
.topbar { padding:18px 32px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:var(--surface); position:sticky; top:0; z-index:50; }
.topbar-title { font-family:'Bebas Neue',sans-serif; font-size:26px; letter-spacing:2px; color:var(--text); }
.topbar-actions { display:flex; gap:10px; align-items:center; }
.content { padding:28px 32px; flex:1; }

/* BOTONES */
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:var(--radius); border:none; cursor:pointer; font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600; transition:all .15s; text-decoration:none; }
.btn-primary { background:var(--accent); color:#fff; }
.btn-primary:hover { background:var(--accent2); }
.btn-secondary { background:var(--border); color:var(--text); }
.btn-secondary:hover { background:#333; }
.btn-danger { background:rgba(239,68,68,.15); color:var(--danger); border:1px solid rgba(239,68,68,.3); }
.btn-danger:hover { background:rgba(239,68,68,.25); }
.btn-success { background:rgba(34,197,94,.15); color:var(--success); border:1px solid rgba(34,197,94,.3); }
.btn-sm { padding:5px 10px; font-size:12px; }

/* PANEL */
.panel { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.panel-header { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
.panel-title { font-family:'Bebas Neue',sans-serif; font-size:18px; letter-spacing:1px; color:var(--text); }

/* TABLA */
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; font-size:13px; }
thead th { padding:10px 16px; text-align:left; font-size:10px; text-transform:uppercase; letter-spacing:1.5px; color:var(--muted); border-bottom:1px solid var(--border); white-space:nowrap; }
tbody tr { border-bottom:1px solid rgba(42,42,42,.6); transition:background .1s; }
tbody tr:hover { background:rgba(249,115,22,.04); }
tbody tr:last-child { border-bottom:none; }
td { padding:11px 16px; vertical-align:middle; }
.td-mono { font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--accent); }
.td-muted { color:var(--muted); font-size:12px; }

/* BADGES */
.badge { display:inline-block; padding:3px 9px; border-radius:4px; font-size:10px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; font-family:'JetBrains Mono',monospace; }
.badge-recibido    { background:rgba(59,130,246,.15); color:var(--info); }
.badge-en_proceso  { background:rgba(234,179,8,.15);  color:var(--warning); }
.badge-terminado   { background:rgba(249,115,22,.15); color:var(--accent); }
.badge-entregado   { background:rgba(34,197,94,.15);  color:var(--success); }
.badge-cancelado   { background:rgba(239,68,68,.15);  color:var(--danger); }
.badge-pendiente   { background:rgba(107,107,107,.2); color:#aaa; }
.badge-confirmada  { background:rgba(59,130,246,.15); color:var(--info); }
.badge-en_curso    { background:rgba(234,179,8,.15);  color:var(--warning); }
.badge-completada  { background:rgba(34,197,94,.15);  color:var(--success); }

/* FILTROS */
.filtros { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.filtros input,.filtros select { background:var(--surface); border:1px solid var(--border); color:var(--text); padding:7px 12px; border-radius:var(--radius); font-family:'DM Sans',sans-serif; font-size:13px; outline:none; transition:border-color .2s; }
.filtros input:focus,.filtros select:focus { border-color:var(--accent); }
.filtro-tab { padding:7px 14px; border-radius:var(--radius); border:1px solid var(--border); cursor:pointer; font-size:12px; font-weight:600; color:var(--muted); background:var(--surface); transition:all .15s; }
.filtro-tab:hover { color:var(--text); border-color:#444; }
.filtro-tab.active { background:var(--accent); color:#fff; border-color:var(--accent); }

/* MODAL */
.overlay { position:fixed; inset:0; background:rgba(0,0,0,.75); z-index:200; display:none; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px); }
.overlay.show { display:flex; }
.modal { background:var(--card); border:1px solid var(--border); border-radius:12px; width:100%; max-width:580px; max-height:90vh; overflow-y:auto; animation:slideUp .2s ease; }
@keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header { padding:20px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
.modal-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; color:var(--accent); }
.modal-close { background:none; border:none; color:var(--muted); font-size:22px; cursor:pointer; transition:color .15s; line-height:1; }
.modal-close:hover { color:var(--text); }
.modal-body { padding:24px; }
.modal-footer { padding:16px 24px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; }

/* FORMULARIO */
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-full { grid-column:1/-1; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); font-weight:600; }
.form-control { background:var(--surface); border:1px solid var(--border); color:var(--text); padding:9px 12px; border-radius:var(--radius); font-family:'DM Sans',sans-serif; font-size:13px; outline:none; transition:border-color .2s; width:100%; }
.form-control:focus { border-color:var(--accent); }
.form-control option { background:var(--card); }

/* TOAST */
#toast { position:fixed; bottom:24px; right:24px; z-index:999; display:flex; flex-direction:column; gap:8px; }
.toast-item { background:var(--card); border:1px solid var(--border); border-left:3px solid var(--success); padding:12px 18px; border-radius:var(--radius); font-size:13px; min-width:220px; animation:fadeIn .2s ease; box-shadow:0 4px 20px rgba(0,0,0,.4); }
.toast-item.error { border-left-color:var(--danger); }
@keyframes fadeIn { from{transform:translateX(20px);opacity:0} to{transform:translateX(0);opacity:1} }

/* EMPTY STATE */
.empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
.empty-state .icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; }

/* LOADING */
.loading { text-align:center; padding:40px; color:var(--muted); font-size:13px; }
.spinner { display:inline-block; width:20px; height:20px; border:2px solid var(--border); border-top-color:var(--accent); border-radius:50%; animation:spin .6s linear infinite; margin-right:8px; vertical-align:middle; }
@keyframes spin { to{transform:rotate(360deg)} }

/* STATS */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:16px; margin-bottom:28px; }
.stat-card { background:var(--card); border:1px solid var(--border); border-radius:var(--radius); padding:20px; transition:border-color .2s; }
.stat-card:hover { border-color:var(--accent); }
.stat-icon { font-size:28px; margin-bottom:10px; display:block; }
.stat-value { font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px; line-height:1; color:var(--accent); }
.stat-label { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-top:4px; }

/* STOCK BAR */
.stock-bar { display:flex; align-items:center; gap:8px; font-size:12px; }
.stock-fill { height:6px; border-radius:3px; background:var(--border); flex:1; overflow:hidden; min-width:60px; }
.stock-fill-inner { height:100%; border-radius:3px; background:var(--success); transition:width .3s; }
.stock-fill-inner.low { background:var(--warning); }
.stock-fill-inner.empty { background:var(--danger); }
</style>
</head>
<body>

<aside id="sidebar">
    <div class="logo">
        <div class="logo-title">⚙ APEX GARAGE</div>
        <div class="logo-sub">Sistema de Gestión</div>
    </div>
    <nav>
        <div class="nav-section">Principal</div>
        <a class="nav-item <?= $paginaActual==='dashboard' ? 'active' : '' ?>" href="index.php">
            <span class="icon">📊</span> Dashboard
        </a>
        <div class="nav-section">Operaciones</div>
        <a class="nav-item <?= $paginaActual==='citas' ? 'active' : '' ?>" href="citas.php">
            <span class="icon">📅</span> Citas
            <span class="badge-nav" id="badge-citas" style="display:none">0</span>
        </a>
        <a class="nav-item <?= $paginaActual==='ordenes' ? 'active' : '' ?>" href="ordenes.php">
            <span class="icon">🔧</span> Órdenes
        </a>
        <div class="nav-section">Catálogos</div>
        <a class="nav-item <?= $paginaActual==='clientes' ? 'active' : '' ?>" href="clientes.php">
            <span class="icon">👤</span> Clientes
        </a>
        <a class="nav-item <?= $paginaActual==='vehiculos' ? 'active' : '' ?>" href="vehiculos.php">
            <span class="icon">🚗</span> Vehículos
        </a>
        <a class="nav-item <?= $paginaActual==='inventario' ? 'active' : '' ?>" href="inventario.php">
            <span class="icon">📦</span> Inventario
            <span class="badge-nav" id="badge-stock" style="display:none">!</span>
        <a class="nav-item <?= $paginaActual==='mecanicos' ? 'active' : '' ?>" href="mecanicos.php">
            <span class="icon">🔩</span> Mecánicos
        </a>
    </nav>
</aside>

<main id="main">
<div id="toast"></div>

<script>
// API helper global
async function api(params, metodo='GET') {
    try {
        let url='api.php', opts={};
        if(metodo==='GET') { url+='?'+new URLSearchParams(params); }
        else { opts={method:'POST',body:new URLSearchParams(params)}; }
        const r=await fetch(url,opts);
        return await r.json();
    } catch(e) { toast('Error de conexión',true); return {ok:false}; }
}
function toast(msg,error=false) {
    const t=document.createElement('div');
    t.className='toast-item'+(error?' error':'');
    t.textContent=(error?'✕ ':'✓ ')+msg;
    document.getElementById('toast').appendChild(t);
    setTimeout(()=>t.remove(),3500);
}
function abrirModal(id) { document.getElementById(id).classList.add('show'); }
function cerrarModal(id) { document.getElementById(id).classList.remove('show'); }
document.addEventListener('DOMContentLoaded',()=>{
    document.querySelectorAll('.overlay').forEach(o=>{
        o.addEventListener('click',e=>{ if(e.target===o) cerrarModal(o.id); });
    });
});
// Badge citas de hoy
(async()=>{
    const d=await api({accion:'dashboard'});
    if(d.ok){
        const b=document.getElementById('badge-citas');
        if(d.datos.citas_hoy>0){ b.style.display=''; b.textContent=d.datos.citas_hoy; }
        const bs=document.getElementById('badge-stock');
        if(d.datos.stock_bajo>0){ bs.style.display=''; bs.textContent=d.datos.stock_bajo; }
    }
})();
</script>
