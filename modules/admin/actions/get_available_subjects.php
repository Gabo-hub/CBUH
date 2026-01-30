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

    // Obtener el periodo académico activo actual
    $periodoStmt = $pdo->query("
        SELECT id FROM periodos_academicos 
        WHERE estado_id = 1 
        ORDER BY fecha_inicio DESC 
        LIMIT 1
    ");
    $periodo = $periodoStmt->fetch();

    if (!$periodo) {
        echo json_encode(['subjects' => [], 'message' => 'No hay periodo académico activo']);
        exit;
    }

    // Obtener todas las cargas académicas del periodo actual
    $query = "
        SELECT 
            ca.id as carga_id,
            m.id as materia_id,
            m.codigo,
            m.nombre,
            m.año_materia,
            d.nombres as docente_nombres,
            d.apellidos as docente_apellidos,
            s.nombre as seccion
        FROM cargas_academicas ca
        INNER JOIN materias m ON ca.materia_id = m.id
        LEFT JOIN docentes d ON ca.docente_id = d.id
        LEFT JOIN secciones s ON ca.seccion_id = s.id
        WHERE ca.periodo_id = ?
        AND ca.estado_id = 1
        AND m.estado_id = 1
        ORDER BY m.año_materia, m.nombre
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$periodo['id']]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear datos
    foreach ($subjects as &$subject) {
        $subject['año_materia'] = intval($subject['año_materia']);
        $subject['docente'] = trim(($subject['docente_nombres'] ?? '') . ' ' . ($subject['docente_apellidos'] ?? ''));
        $subject['display_name'] = $subject['nombre'] . ' (' . $subject['codigo'] . ')';
        if ($subject['seccion']) {
            $subject['display_name'] .= ' - Sección ' . $subject['seccion'];
        }
    }

    echo json_encode([
        'success' => true,
        'subjects' => $subjects
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al cargar materias: ' . $e->getMessage()]);
}
