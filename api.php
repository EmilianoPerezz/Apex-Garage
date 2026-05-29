<?php
// api.php  — Endpoint único para todas las operaciones del taller
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

// ── Helpers ──────────────────────────────────────────────────────────────────
function responder($ok, $datos = [], $mensaje = '') {
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje, 'datos' => $datos]);
    exit;
}

function escape($conexion, $valor) {
    return $conexion->real_escape_string(trim($valor));
}

// ── ENRUTADOR ────────────────────────────────────────────────────────────────
switch ($accion) {

    // ── DASHBOARD ────────────────────────────────────────────────────────────
    case 'dashboard':
        $stats = [];

        $r = $conexion->query("SELECT COUNT(*) c FROM CLIENTE WHERE activo=1");
        $stats['clientes'] = $r->fetch_assoc()['c'];

        $r = $conexion->query("SELECT COUNT(*) c FROM VEHICULO");
        $stats['vehiculos'] = $r->fetch_assoc()['c'];

        $r = $conexion->query("SELECT COUNT(*) c FROM CITA WHERE fecha_cita = CURDATE() AND estatus IN ('PENDIENTE','CONFIRMADA')");
        $stats['citas_hoy'] = $r->fetch_assoc()['c'];

        $r = $conexion->query("SELECT COUNT(*) c FROM ORDEN_SERVICIO WHERE estatus IN ('RECIBIDO','EN_PROCESO')");
        $stats['ordenes_activas'] = $r->fetch_assoc()['c'];

        $r = $conexion->query("SELECT COUNT(*) c FROM INVENTARIO WHERE cantidad <= stock_minimo");
        $stats['stock_bajo'] = $r->fetch_assoc()['c'];

        $r = $conexion->query("SELECT COALESCE(SUM(total),0) t FROM ORDEN_SERVICIO WHERE estatus='ENTREGADO' AND MONTH(fecha_entrada)=MONTH(NOW()) AND pagado=1");
        $stats['ingresos_mes'] = $r->fetch_assoc()['t'];

        responder(true, $stats);


    // ── CLIENTES ─────────────────────────────────────────────────────────────
    case 'listar_clientes':
        $buscar = isset($_GET['q']) ? '%' . escape($conexion, $_GET['q']) . '%' : '%';
        $sql = "SELECT id_cliente, nombre, apellido_pat, apellido_mat, telefono,
                       telefono_alt, correo, direccion, fecha_registro, activo
                FROM CLIENTE
                WHERE activo=1
                  AND (nombre LIKE '$buscar' OR apellido_pat LIKE '$buscar' OR telefono LIKE '$buscar')
                ORDER BY apellido_pat, nombre";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_cliente':
        $nombre      = escape($conexion, $_POST['nombre']       ?? '');
        $ap_pat      = escape($conexion, $_POST['apellido_pat'] ?? '');
        $ap_mat      = escape($conexion, $_POST['apellido_mat'] ?? '');
        $tel         = escape($conexion, $_POST['telefono']     ?? '');
        $tel_alt     = escape($conexion, $_POST['telefono_alt'] ?? '');
        $correo      = escape($conexion, $_POST['correo']       ?? '');
        $direccion   = escape($conexion, $_POST['direccion']    ?? '');
        $rfc         = escape($conexion, $_POST['rfc']          ?? '');

        if (!$nombre || !$ap_pat || !$tel)
            responder(false, [], 'Nombre, apellido y teléfono son obligatorios');

        $id = (int)($_POST['id_cliente'] ?? 0);

        if ($id) {
            $sql = "UPDATE CLIENTE SET nombre='$nombre', apellido_pat='$ap_pat',
                    apellido_mat='$ap_mat', telefono='$tel', telefono_alt='$tel_alt',
                    correo='$correo', direccion='$direccion', rfc='$rfc'
                    WHERE id_cliente=$id";
            $conexion->query($sql);
            responder(true, ['id_cliente' => $id], 'Cliente actualizado');
        } else {
            $sql = "INSERT INTO CLIENTE (nombre,apellido_pat,apellido_mat,telefono,telefono_alt,correo,direccion,rfc)
                    VALUES ('$nombre','$ap_pat','$ap_mat','$tel','$tel_alt','$correo','$direccion','$rfc')";
            $conexion->query($sql);
            responder(true, ['id_cliente' => $conexion->insert_id], 'Cliente guardado');
        }


    // ── VEHÍCULOS ─────────────────────────────────────────────────────────────
    case 'listar_vehiculos':
        $id_cliente = (int)($_GET['id_cliente'] ?? 0);
        $where = $id_cliente ? "WHERE v.id_cliente=$id_cliente" : '';
        $sql = "SELECT v.*, CONCAT(c.nombre,' ',c.apellido_pat) AS propietario
                FROM VEHICULO v
                JOIN CLIENTE c ON c.id_cliente = v.id_cliente
                $where
                ORDER BY v.marca, v.modelo";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_vehiculo':
        $id_cliente  = (int)($_POST['id_cliente']   ?? 0);
        $marca       = escape($conexion, $_POST['marca']       ?? '');
        $modelo      = escape($conexion, $_POST['modelo']      ?? '');
        $anio        = (int)($_POST['anio']         ?? date('Y'));
        $color       = escape($conexion, $_POST['color']       ?? '');
        $placa       = strtoupper(escape($conexion, $_POST['placa'] ?? ''));
        $vin         = escape($conexion, $_POST['numero_serie'] ?? '');
        $km          = (int)($_POST['km_actual']    ?? 0);
        $trans       = escape($conexion, $_POST['transmision'] ?? 'MANUAL');
        $comb        = escape($conexion, $_POST['combustible'] ?? 'GASOLINA');

        if (!$id_cliente || !$marca || !$modelo || !$placa)
            responder(false, [], 'Faltan campos obligatorios');

        $id = (int)($_POST['id_vehiculo'] ?? 0);
        if ($id) {
            $sql = "UPDATE VEHICULO SET id_cliente=$id_cliente, marca='$marca', modelo='$modelo',
                    anio=$anio, color='$color', placa='$placa', numero_serie='$vin',
                    km_actual=$km, transmision='$trans', combustible='$comb'
                    WHERE id_vehiculo=$id";
            $conexion->query($sql);
            responder(true, ['id_vehiculo' => $id], 'Vehículo actualizado');
        } else {
            $sql = "INSERT INTO VEHICULO (id_cliente,marca,modelo,anio,color,placa,numero_serie,km_actual,transmision,combustible)
                    VALUES ($id_cliente,'$marca','$modelo',$anio,'$color','$placa','$vin',$km,'$trans','$comb')";
            if ($conexion->query($sql))
                responder(true, ['id_vehiculo' => $conexion->insert_id], 'Vehículo guardado');
            else
                responder(false, [], 'Error: ' . $conexion->error);
        }


    // ── CITAS ─────────────────────────────────────────────────────────────────
    case 'listar_citas':
        $filtro = escape($conexion, $_GET['filtro'] ?? 'proximas');
        if ($filtro === 'hoy')
            $where = "WHERE c.fecha_cita = CURDATE()";
        elseif ($filtro === 'semana')
            $where = "WHERE c.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        elseif ($filtro === 'todas')
            $where = "WHERE c.fecha_cita >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        else
            $where = "WHERE c.fecha_cita >= CURDATE() AND c.estatus NOT IN ('COMPLETADA','CANCELADA')";

        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.estatus, c.motivo, c.notas,
                       cl.id_cliente, CONCAT(cl.nombre,' ',cl.apellido_pat) AS cliente,
                       cl.telefono,
                       v.id_vehiculo, CONCAT(v.marca,' ',v.modelo,' ',v.anio) AS vehiculo, v.placa,
                       CONCAT(m.nombre,' ',m.apellido_pat) AS mecanico
                FROM CITA c
                JOIN CLIENTE  cl ON cl.id_cliente  = c.id_cliente
                JOIN VEHICULO  v ON v.id_vehiculo  = c.id_vehiculo
                LEFT JOIN MECANICO m ON m.id_mecanico = c.id_mecanico
                $where
                ORDER BY c.fecha_cita, c.hora_cita";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_cita':
        $id_cliente  = (int)($_POST['id_cliente']  ?? 0);
        $id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);
        $id_mecanico = (int)($_POST['id_mecanico'] ?? 0) ?: 'NULL';
        $fecha       = escape($conexion, $_POST['fecha_cita']  ?? '');
        $hora        = escape($conexion, $_POST['hora_cita']   ?? '');
        $motivo      = escape($conexion, $_POST['motivo']      ?? '');
        $notas       = escape($conexion, $_POST['notas']       ?? '');
        $estatus     = escape($conexion, $_POST['estatus']     ?? 'PENDIENTE');

        if (!$id_cliente || !$id_vehiculo || !$fecha || !$hora || !$motivo)
            responder(false, [], 'Faltan campos obligatorios');

        $id = (int)($_POST['id_cita'] ?? 0);
        if ($id) {
            $sql = "UPDATE CITA SET id_cliente=$id_cliente, id_vehiculo=$id_vehiculo,
                    id_mecanico=$id_mecanico, fecha_cita='$fecha', hora_cita='$hora',
                    motivo='$motivo', notas='$notas', estatus='$estatus'
                    WHERE id_cita=$id";
            $conexion->query($sql);
            responder(true, ['id_cita' => $id], 'Cita actualizada');
        } else {
            $sql = "INSERT INTO CITA (id_cliente,id_vehiculo,id_mecanico,fecha_cita,hora_cita,motivo,notas,estatus)
                    VALUES ($id_cliente,$id_vehiculo,$id_mecanico,'$fecha','$hora','$motivo','$notas','$estatus')";
            $conexion->query($sql);
            responder(true, ['id_cita' => $conexion->insert_id], 'Cita agendada');
        }

    case 'cancelar_cita':
        $id = (int)($_POST['id_cita'] ?? 0);
        if (!$id) responder(false, [], 'ID inválido');
        $conexion->query("UPDATE CITA SET estatus='CANCELADA' WHERE id_cita=$id");
        responder(true, [], 'Cita cancelada');


    // ── ÓRDENES DE SERVICIO ───────────────────────────────────────────────────
    case 'listar_ordenes':
        $estatus = escape($conexion, $_GET['estatus'] ?? '');
        $where   = $estatus ? "WHERE o.estatus='$estatus'" : "WHERE o.estatus != 'ENTREGADO'";
        $sql = "SELECT o.id_orden, o.fecha_entrada, o.estatus, o.total, o.pagado,
                       o.descripcion, o.km_entrada,
                       CONCAT(cl.nombre,' ',cl.apellido_pat) AS cliente, cl.telefono,
                       CONCAT(v.marca,' ',v.modelo,' ',v.anio) AS vehiculo, v.placa,
                       CONCAT(m.nombre,' ',m.apellido_pat) AS mecanico
                FROM ORDEN_SERVICIO o
                JOIN VEHICULO  v  ON v.id_vehiculo  = o.id_vehiculo
                JOIN CLIENTE   cl ON cl.id_cliente  = v.id_cliente
                LEFT JOIN MECANICO m ON m.id_mecanico = o.id_mecanico
                $where
                ORDER BY o.fecha_entrada DESC";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_orden':
        $id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);
        $id_mecanico = (int)($_POST['id_mecanico'] ?? 0) ?: 'NULL';
        $id_cita     = (int)($_POST['id_cita']     ?? 0) ?: 'NULL';
        $km          = (int)($_POST['km_entrada']  ?? 0);
        $descripcion = escape($conexion, $_POST['descripcion']    ?? '');
        $notas       = escape($conexion, $_POST['notas_internas'] ?? '');
        $estatus     = escape($conexion, $_POST['estatus']        ?? 'RECIBIDO');

        if (!$id_vehiculo || !$descripcion)
            responder(false, [], 'Vehículo y descripción son obligatorios');

        $id = (int)($_POST['id_orden'] ?? 0);
        if ($id) {
            $sql = "UPDATE ORDEN_SERVICIO SET id_vehiculo=$id_vehiculo, id_mecanico=$id_mecanico,
                    km_entrada=$km, descripcion='$descripcion', notas_internas='$notas', estatus='$estatus'
                    WHERE id_orden=$id";
            $conexion->query($sql);
            responder(true, ['id_orden' => $id], 'Orden actualizada');
        } else {
            $sql = "INSERT INTO ORDEN_SERVICIO (id_vehiculo,id_mecanico,id_cita,km_entrada,descripcion,notas_internas,estatus)
                    VALUES ($id_vehiculo,$id_mecanico,$id_cita,$km,'$descripcion','$notas','$estatus')";
            $conexion->query($sql);
            responder(true, ['id_orden' => $conexion->insert_id], 'Orden creada');
        }

    case 'cambiar_estatus_orden':
        $id      = (int)($_POST['id_orden'] ?? 0);
        $estatus = escape($conexion, $_POST['estatus'] ?? '');
        if (!$id || !$estatus) responder(false, [], 'Datos inválidos');
        $extra = ($estatus === 'ENTREGADO') ? ", fecha_entrega=NOW()" : '';
        $conexion->query("UPDATE ORDEN_SERVICIO SET estatus='$estatus'$extra WHERE id_orden=$id");
        responder(true, [], 'Estatus actualizado');


    // ── MECÁNICOS ─────────────────────────────────────────────────────────────
    case 'listar_mecanicos':
        $r = $conexion->query("SELECT * FROM MECANICO WHERE activo=1 ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);


    // ── INVENTARIO ────────────────────────────────────────────────────────────
    case 'listar_inventario':
        $r = $conexion->query("SELECT *, (cantidad <= stock_minimo) AS stock_critico FROM INVENTARIO ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);


    // ── CATÁLOGO DE SERVICIOS ─────────────────────────────────────────────────
    case 'listar_servicios':
        $r = $conexion->query("SELECT * FROM CATALOGO_SERVICIO WHERE activo=1 ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);


    default:
        responder(false, [], 'Acción no reconocida: ' . $accion);
}
?>
