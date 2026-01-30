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

    $searchTerm = $_GET['q'] ?? '';

    if (empty($searchTerm)) {
        echo json_encode(['teachers' => []]);
        exit;
    }

    // Obtener el periodo académico activo
    $periodoStmt = $pdo->query("
        SELECT id FROM periodos_academicos 
        WHERE estado_id = 1 
        ORDER BY fecha_inicio DESC 
        LIMIT 1
    ");
    $periodo = $periodoStmt->fetch();
    $periodoId = $periodo ? $periodo['id'] : null;

    // Búsqueda por nombre, apellido o cédula
    $query = "
        SELECT 
            d.id,
            d.cedula,
            d.nombres,
            d.apellidos,
            d.especialidad,
            d.telefono,
            u.url_foto,
            COUNT(DISTINCT ca.materia_id) as materias_count,
            GROUP_CONCAT(DISTINCT m.nombre ORDER BY m.nombre SEPARATOR '|') as materias_nombres
        FROM docentes d
        LEFT JOIN usuarios u ON d.usuario_id = u.id
        LEFT JOIN cargas_academicas ca ON d.id = ca.docente_id 
            AND ca.estado_id = 1
    ";

    if ($periodoId) {
        $query .= " AND ca.periodo_id = " . intval($periodoId);
    }

    $query .= "
        LEFT JOIN materias m ON ca.materia_id = m.id AND m.estado_id = 1
        WHERE d.estado_id = 1
        AND (
            d.nombres LIKE ? OR
            d.apellidos LIKE ? OR
            d.cedula LIKE ? OR
            CONCAT(d.nombres, ' ', d.apellidos) LIKE ?
        )
        GROUP BY d.id, d.cedula, d.nombres, d.apellidos, d.especialidad, d.telefono, u.url_foto
        ORDER BY d.apellidos, d.nombres
        LIMIT 20
    ";

    $searchPattern = '%' . $searchTerm . '%';
    $stmt = $pdo->prepare($query);
    $stmt->execute([$searchPattern, $searchPattern, $searchPattern, $searchPattern]);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatear datos
    foreach ($teachers as &$teacher) {
        $teacher['nombre_completo'] = trim($teacher['nombres'] . ' ' . $teacher['apellidos']);
        $teacher['materias_count'] = intval($teacher['materias_count']);

        if ($teacher['materias_nombres']) {
            $teacher['materias'] = explode('|', $teacher['materias_nombres']);
        } else {
            $teacher['materias'] = [];
        }
        unset($teacher['materias_nombres']);

        // Estado de carga
        if ($teacher['materias_count'] == 0) {
            $teacher['carga_estado'] = 'empty';
        } elseif ($teacher['materias_count'] >= 5) {
            $teacher['carga_estado'] = 'full';
        } elseif ($teacher['materias_count'] >= 4) {
            $teacher['carga_estado'] = 'high';
        } else {
            $teacher['carga_estado'] = 'normal';
        }
    }

    echo json_encode([
        'success' => true,
        'teachers' => $teachers,
        'total' => count($teachers)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
}
