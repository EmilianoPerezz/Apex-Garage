<?php
// api.php — Endpoint único para todas las operaciones de Apex Garage
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

function responder($ok, $datos = [], $mensaje = '') {
    echo json_encode(['ok' => $ok, 'mensaje' => $mensaje, 'datos' => $datos]);
    exit;
}

function esc($c, $v) {
    return $c->real_escape_string(trim($v ?? ''));
}

switch ($accion) {

    // ── DASHBOARD ─────────────────────────────────────────────
    case 'dashboard':
        $s = [];
        $s['clientes']       = $conexion->query("SELECT COUNT(*) c FROM CLIENTE WHERE activo=1")->fetch_assoc()['c'];
        $s['vehiculos']      = $conexion->query("SELECT COUNT(*) c FROM VEHICULO")->fetch_assoc()['c'];
        $s['citas_hoy']      = $conexion->query("SELECT COUNT(*) c FROM CITA WHERE fecha_cita=CURDATE() AND estatus IN('PENDIENTE','CONFIRMADA')")->fetch_assoc()['c'];
        $s['ordenes_activas']= $conexion->query("SELECT COUNT(*) c FROM ORDEN_SERVICIO WHERE estatus IN('RECIBIDO','EN_PROCESO')")->fetch_assoc()['c'];
        $s['stock_bajo']     = $conexion->query("SELECT COUNT(*) c FROM INVENTARIO WHERE cantidad<=stock_minimo")->fetch_assoc()['c'];
        $s['ingresos_mes']   = $conexion->query("SELECT COALESCE(SUM(total),0) t FROM ORDEN_SERVICIO WHERE estatus='ENTREGADO' AND MONTH(fecha_entrada)=MONTH(NOW()) AND pagado=1")->fetch_assoc()['t'];
        responder(true, $s);

    // ── CLIENTES ──────────────────────────────────────────────
    case 'listar_clientes':
        $q = '%' . esc($conexion, $_GET['q'] ?? '') . '%';
        $r = $conexion->query("SELECT * FROM CLIENTE WHERE activo=1 AND (nombre LIKE '$q' OR apellido_pat LIKE '$q' OR telefono LIKE '$q') ORDER BY apellido_pat,nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_cliente':
        $nombre    = esc($conexion, $_POST['nombre']       ?? '');
        $ap_pat    = esc($conexion, $_POST['apellido_pat'] ?? '');
        $ap_mat    = esc($conexion, $_POST['apellido_mat'] ?? '');
        $tel       = esc($conexion, $_POST['telefono']     ?? '');
        $tel_alt   = esc($conexion, $_POST['telefono_alt'] ?? '');
        $correo    = esc($conexion, $_POST['correo']       ?? '');
        $direccion = esc($conexion, $_POST['direccion']    ?? '');
        $rfc       = esc($conexion, $_POST['rfc']          ?? '');
        if (!$nombre || !$ap_pat || !$tel) responder(false, [], 'Nombre, apellido y teléfono son obligatorios');
        $id = (int)($_POST['id_cliente'] ?? 0);
        if ($id) {
            $conexion->query("UPDATE CLIENTE SET nombre='$nombre',apellido_pat='$ap_pat',apellido_mat='$ap_mat',telefono='$tel',telefono_alt='$tel_alt',correo='$correo',direccion='$direccion',rfc='$rfc' WHERE id_cliente=$id");
            responder(true, ['id_cliente'=>$id], 'Cliente actualizado');
        } else {
            $conexion->query("INSERT INTO CLIENTE (nombre,apellido_pat,apellido_mat,telefono,telefono_alt,correo,direccion,rfc) VALUES ('$nombre','$ap_pat','$ap_mat','$tel','$tel_alt','$correo','$direccion','$rfc')");
            responder(true, ['id_cliente'=>$conexion->insert_id], 'Cliente guardado');
        }

    // ── VEHÍCULOS ─────────────────────────────────────────────
    case 'listar_vehiculos':
        $id_c = (int)($_GET['id_cliente'] ?? 0);
        $where = $id_c ? "WHERE v.id_cliente=$id_c" : '';
        $r = $conexion->query("SELECT v.*, CONCAT(c.nombre,' ',c.apellido_pat) AS propietario, c.telefono FROM VEHICULO v JOIN CLIENTE c ON c.id_cliente=v.id_cliente $where ORDER BY v.marca,v.modelo");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_vehiculo':
        $id_c  = (int)($_POST['id_cliente']   ?? 0);
        $marca = esc($conexion, $_POST['marca']        ?? '');
        $model = esc($conexion, $_POST['modelo']       ?? '');
        $anio  = (int)($_POST['anio']          ?? date('Y'));
        $color = esc($conexion, $_POST['color']        ?? '');
        $placa = strtoupper(esc($conexion, $_POST['placa'] ?? ''));
        $vin   = esc($conexion, $_POST['numero_serie'] ?? '');
        $km    = (int)($_POST['km_actual']     ?? 0);
        $trans = esc($conexion, $_POST['transmision']  ?? 'MANUAL');
        $comb  = esc($conexion, $_POST['combustible']  ?? 'GASOLINA');
        if (!$id_c || !$marca || !$model || !$placa) responder(false, [], 'Faltan campos obligatorios');
        $id = (int)($_POST['id_vehiculo'] ?? 0);
        if ($id) {
            $conexion->query("UPDATE VEHICULO SET id_cliente=$id_c,marca='$marca',modelo='$model',anio=$anio,color='$color',placa='$placa',numero_serie='$vin',km_actual=$km,transmision='$trans',combustible='$comb' WHERE id_vehiculo=$id");
            responder(true, ['id_vehiculo'=>$id], 'Vehículo actualizado');
        } else {
            if ($conexion->query("INSERT INTO VEHICULO (id_cliente,marca,modelo,anio,color,placa,numero_serie,km_actual,transmision,combustible) VALUES ($id_c,'$marca','$model',$anio,'$color','$placa','$vin',$km,'$trans','$comb')"))
                responder(true, ['id_vehiculo'=>$conexion->insert_id], 'Vehículo guardado');
            else responder(false, [], 'Error: '.$conexion->error);
        }

    // ── CITAS ─────────────────────────────────────────────────
    case 'listar_citas':
        $filtro = esc($conexion, $_GET['filtro'] ?? 'proximas');
        if ($filtro==='hoy')
            $where = "WHERE c.fecha_cita=CURDATE()";
        elseif ($filtro==='semana')
            $where = "WHERE c.fecha_cita BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 7 DAY)";
        elseif ($filtro==='todas')
            $where = "WHERE c.fecha_cita >= DATE_SUB(CURDATE(),INTERVAL 30 DAY)";
        else
            $where = "WHERE c.fecha_cita >= CURDATE() AND c.estatus NOT IN('COMPLETADA','CANCELADA')";

        $sql = "SELECT c.id_cita,c.fecha_cita,c.hora_cita,c.estatus,c.motivo,c.notas,
                       cl.id_cliente, CONCAT(cl.nombre,' ',cl.apellido_pat) AS cliente, cl.telefono,
                       v.id_vehiculo, CONCAT(v.marca,' ',v.modelo,' ',v.anio) AS vehiculo, v.placa,
                       CONCAT(m.nombre,' ',m.apellido_pat) AS mecanico
                FROM CITA c
                JOIN CLIENTE  cl ON cl.id_cliente  = c.id_cliente
                JOIN VEHICULO  v ON v.id_vehiculo  = c.id_vehiculo
                LEFT JOIN MECANICO m ON m.id_mecanico = c.id_mecanico
                $where ORDER BY c.fecha_cita, c.hora_cita";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    // Obtener una cita específica para edición
    case 'obtener_cita':
        $id = (int)($_GET['id_cita'] ?? 0);
        if (!$id) responder(false, [], 'ID inválido');
        $r = $conexion->query("SELECT c.*,
                       cl.id_cliente,
                       v.id_vehiculo,
                       CONCAT(v.marca,' ',v.modelo,' ',v.anio) AS vehiculo_nombre
                FROM CITA c
                JOIN CLIENTE cl ON cl.id_cliente=c.id_cliente
                JOIN VEHICULO v ON v.id_vehiculo=c.id_vehiculo
                WHERE c.id_cita=$id");
        $f = $r->fetch_assoc();
        if (!$f) responder(false, [], 'Cita no encontrada');
        responder(true, $f);

    case 'guardar_cita':
        $id_c  = (int)($_POST['id_cliente']  ?? 0);
        $id_v  = (int)($_POST['id_vehiculo'] ?? 0);
        $id_m  = (int)($_POST['id_mecanico'] ?? 0) ?: 'NULL';
        $fecha = esc($conexion, $_POST['fecha_cita'] ?? '');
        $hora  = esc($conexion, $_POST['hora_cita']  ?? '');
        $motiv = esc($conexion, $_POST['motivo']     ?? '');
        $notas = esc($conexion, $_POST['notas']      ?? '');
        $estat = esc($conexion, $_POST['estatus']    ?? 'PENDIENTE');
        if (!$id_c || !$id_v || !$fecha || !$hora || !$motiv) responder(false, [], 'Faltan campos obligatorios');
        $id = (int)($_POST['id_cita'] ?? 0);
        if ($id) {
            $conexion->query("UPDATE CITA SET id_cliente=$id_c,id_vehiculo=$id_v,id_mecanico=$id_m,fecha_cita='$fecha',hora_cita='$hora',motivo='$motiv',notas='$notas',estatus='$estat' WHERE id_cita=$id");
            responder(true, ['id_cita'=>$id], 'Cita actualizada');
        } else {
            $conexion->query("INSERT INTO CITA (id_cliente,id_vehiculo,id_mecanico,fecha_cita,hora_cita,motivo,notas,estatus) VALUES ($id_c,$id_v,$id_m,'$fecha','$hora','$motiv','$notas','$estat')");
            responder(true, ['id_cita'=>$conexion->insert_id], 'Cita agendada');
        }

    case 'cancelar_cita':
        $id = (int)($_POST['id_cita'] ?? 0);
        if (!$id) responder(false, [], 'ID inválido');
        $conexion->query("UPDATE CITA SET estatus='CANCELADA' WHERE id_cita=$id");
        responder(true, [], 'Cita cancelada');

    // ── ÓRDENES ───────────────────────────────────────────────
    case 'listar_ordenes':
        $estatus = esc($conexion, $_GET['estatus'] ?? '');
        $todas   = ($_GET['todas'] ?? '0') === '1';
        if ($todas) {
            $where = "WHERE 1=1";  // sin filtro
        } elseif ($estatus) {
            $where = "WHERE o.estatus='$estatus'";
        } else {
            // "activas" = todo menos entregado y cancelado
            $where = "WHERE o.estatus NOT IN('ENTREGADO','CANCELADO')";
        }
        $sql = "SELECT o.id_orden,o.fecha_entrada,o.estatus,o.total,o.pagado,
                       o.descripcion,o.km_entrada,o.notas_internas,o.id_mecanico,
                       v.id_vehiculo, v.id_cliente,
                       CONCAT(cl.nombre,' ',cl.apellido_pat) AS cliente, cl.telefono,
                       CONCAT(v.marca,' ',v.modelo,' ',v.anio) AS vehiculo, v.placa,
                       CONCAT(m.nombre,' ',m.apellido_pat) AS mecanico
                FROM ORDEN_SERVICIO o
                JOIN VEHICULO  v  ON v.id_vehiculo  = o.id_vehiculo
                JOIN CLIENTE   cl ON cl.id_cliente  = v.id_cliente
                LEFT JOIN MECANICO m ON m.id_mecanico = o.id_mecanico
                $where ORDER BY o.fecha_entrada DESC";
        $r = $conexion->query($sql);
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    // Obtener una orden específica para edición
    case 'obtener_orden':
        $id = (int)($_GET['id_orden'] ?? 0);
        if (!$id) responder(false, [], 'ID inválido');
        $r = $conexion->query("SELECT o.*, v.id_cliente
                FROM ORDEN_SERVICIO o
                JOIN VEHICULO v ON v.id_vehiculo=o.id_vehiculo
                WHERE o.id_orden=$id");
        $f = $r->fetch_assoc();
        if (!$f) responder(false, [], 'Orden no encontrada');
        responder(true, $f);

    case 'guardar_orden':
        $id_v  = (int)($_POST['id_vehiculo']    ?? 0);
        $id_m  = (int)($_POST['id_mecanico']    ?? 0) ?: 'NULL';
        $id_ci = (int)($_POST['id_cita']        ?? 0) ?: 'NULL';
        $km    = (int)($_POST['km_entrada']     ?? 0);
        $desc  = esc($conexion, $_POST['descripcion']    ?? '');
        $notas = esc($conexion, $_POST['notas_internas'] ?? '');
        $estat = esc($conexion, $_POST['estatus']        ?? 'RECIBIDO');
        $total = (float)($_POST['total'] ?? 0);
        if (!$id_v || !$desc) responder(false, [], 'Vehículo y descripción son obligatorios');
        $id = (int)($_POST['id_orden'] ?? 0);
        if ($id) {
            $conexion->query("UPDATE ORDEN_SERVICIO SET id_vehiculo=$id_v,id_mecanico=$id_m,km_entrada=$km,descripcion='$desc',notas_internas='$notas',estatus='$estat',total=$total WHERE id_orden=$id");
            responder(true, ['id_orden'=>$id], 'Orden actualizada');
        } else {
            $conexion->query("INSERT INTO ORDEN_SERVICIO (id_vehiculo,id_mecanico,id_cita,km_entrada,descripcion,notas_internas,estatus,total) VALUES ($id_v,$id_m,$id_ci,$km,'$desc','$notas','$estat',$total)");
            responder(true, ['id_orden'=>$conexion->insert_id], 'Orden creada');
        }

    case 'cambiar_estatus_orden':
        $id    = (int)($_POST['id_orden'] ?? 0);
        $estat = esc($conexion, $_POST['estatus'] ?? '');
        if (!$id || !$estat) responder(false, [], 'Datos inválidos');
        $extra = ($estat === 'ENTREGADO') ? ",fecha_entrega=NOW()" : '';
        $conexion->query("UPDATE ORDEN_SERVICIO SET estatus='$estat'$extra WHERE id_orden=$id");
        responder(true, [], 'Estatus actualizado');

    // ── MECÁNICOS ─────────────────────────────────────────────
    case 'listar_mecanicos':
        $r = $conexion->query("SELECT * FROM MECANICO WHERE activo=1 ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    // ── INVENTARIO ────────────────────────────────────────────
    case 'listar_inventario':
        $r = $conexion->query("SELECT *, (cantidad<=stock_minimo) AS stock_critico FROM INVENTARIO ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    case 'guardar_inventario':
        $nombre    = esc($conexion, $_POST['nombre']        ?? '');
        $desc      = esc($conexion, $_POST['descripcion']   ?? '');
        $noparte   = esc($conexion, $_POST['numero_parte']  ?? '');
        $cantidad  = (int)($_POST['cantidad']      ?? 0);
        $p_compra  = (float)($_POST['precio_compra'] ?? 0);
        $p_venta   = (float)($_POST['precio_venta']  ?? 0);
        $stock_min = (int)($_POST['stock_minimo']   ?? 5);
        $proveedor = esc($conexion, $_POST['proveedor']     ?? '');
        $ubicacion = esc($conexion, $_POST['ubicacion']     ?? '');
        if (!$nombre) responder(false, [], 'El nombre es obligatorio');
        $id = (int)($_POST['id_refaccion'] ?? 0);
        if ($id) {
            $conexion->query("UPDATE INVENTARIO SET nombre='$nombre',descripcion='$desc',numero_parte='$noparte',cantidad=$cantidad,precio_compra=$p_compra,precio_venta=$p_venta,stock_minimo=$stock_min,proveedor='$proveedor',ubicacion='$ubicacion' WHERE id_refaccion=$id");
            responder(true, ['id_refaccion'=>$id], 'Refacción actualizada');
        } else {
            $conexion->query("INSERT INTO INVENTARIO (nombre,descripcion,numero_parte,cantidad,precio_compra,precio_venta,stock_minimo,proveedor,ubicacion) VALUES ('$nombre','$desc','$noparte',$cantidad,$p_compra,$p_venta,$stock_min,'$proveedor','$ubicacion')");
            responder(true, ['id_refaccion'=>$conexion->insert_id], 'Refacción guardada');
        }

    case 'eliminar_inventario':
        $id = (int)($_POST['id_refaccion'] ?? 0);
        if (!$id) responder(false, [], 'ID inválido');
        // Verificar si está en uso en alguna orden
        $uso = $conexion->query("SELECT COUNT(*) c FROM DETALLE_ORDEN WHERE tipo='REFACCION' AND id_referencia=$id")->fetch_assoc()['c'];
        if ($uso > 0) responder(false, [], "No se puede eliminar: está registrada en $uso orden(es)");
        $conexion->query("DELETE FROM INVENTARIO WHERE id_refaccion=$id");
        responder(true, [], 'Refacción eliminada');

    // ── MECANICO ────────────────────────────────────────────
    case 'guardar_mecanico':
    $nombre   = esc($conexion, $_POST['nombre']       ?? '');
    $ap_pat   = esc($conexion, $_POST['apellido_pat'] ?? '');
    $ap_mat   = esc($conexion, $_POST['apellido_mat'] ?? '');
    $tel      = esc($conexion, $_POST['telefono']     ?? '');
    $espec    = esc($conexion, $_POST['especialidad'] ?? '');
    $salario  = (float)($_POST['salario'] ?? 0);        // <-- campo nuevo en BD
    $id = (int)($_POST['id_mecanico'] ?? 0);
    if ($id) {
        $conexion->query("UPDATE MECANICO SET nombre='$nombre', apellido_pat='$ap_pat',
            apellido_mat='$ap_mat', telefono='$tel', especialidad='$espec', salario=$salario
            WHERE id_mecanico=$id");
        responder(true, [], 'Mecánico actualizado');
    } else {
        $conexion->query("INSERT INTO MECANICO (nombre,apellido_pat,apellido_mat,telefono,especialidad,salario)
            VALUES ('$nombre','$ap_pat','$ap_mat','$tel','$espec',$salario)");
        responder(true, ['id_mecanico'=>$conexion->insert_id], 'Mecánico guardado');
    }
    

    // ── CATÁLOGO DE SERVICIOS ─────────────────────────────────
    case 'listar_servicios':
        $r = $conexion->query("SELECT * FROM CATALOGO_SERVICIO WHERE activo=1 ORDER BY nombre");
        $lista = [];
        while ($f = $r->fetch_assoc()) $lista[] = $f;
        responder(true, $lista);

    default:
        responder(false, [], 'Acción no reconocida: ' . $accion);
}
?>
