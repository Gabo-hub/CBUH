<?php
session_start();
require_once '../../../config/database.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();

    // Obtener filtros opcionales
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;

    // Obtener el periodo académico activo actual
    $periodoStmt = $pdo->query("
        SELECT id FROM periodos_academicos 
        WHERE estado_id = 1 
        ORDER BY fecha_inicio DESC 
        LIMIT 1
    ");
    $periodo = $periodoStmt->fetch();

    if (!$periodo) {
        echo json_encode(['schedules' => [], 'message' => 'No hay periodo académico activo']);
        exit;
    }

    // Query para obtener horarios con información completa
    $query = "
        SELECT 
            h.id,
            h.dia_semana,
            h.hora_inicio,
            h.hora_fin,
            h.aula,
            m.id as materia_id,
            m.nombre as materia_nombre,
            m.codigo as materia_codigo,
            m.año_materia,
            d.nombres as docente_nombres,
            d.apellidos as docente_apellidos,
            s.nombre as seccion_nombre,
            ca.id as carga_id
        FROM horarios h
        INNER JOIN cargas_academicas ca ON h.carga_academica_id = ca.id
        INNER JOIN materias m ON ca.materia_id = m.id
        LEFT JOIN docentes d ON ca.docente_id = d.id
        LEFT JOIN secciones s ON ca.seccion_id = s.id
        WHERE ca.periodo_id = ? 
        AND h.estado_id = 1
        AND ca.estado_id = 1
    ";

    $params = [$periodo['id']];

    // Aplicar filtro de año si se especifica
    if ($year !== null) {
        $query .= " AND m.año_materia = ?";
        $params[] = $year;
    }

    $query .= " ORDER BY h.dia_semana, h.hora_inicio";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear las fechas para el frontend
    foreach ($schedules as &$schedule) {
        $schedule['dia_semana'] = intval($schedule['dia_semana']);
        $schedule['año_materia'] = intval($schedule['año_materia']);
        $schedule['docente_completo'] = trim(($schedule['docente_nombres'] ?? '') . ' ' . ($schedule['docente_apellidos'] ?? ''));
    }

    echo json_encode([
        'success' => true,
        'schedules' => $schedules,
        'periodo_id' => $periodo['id']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar horarios: ' . $e->getMessage()]);
}
