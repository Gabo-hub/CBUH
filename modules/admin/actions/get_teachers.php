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
        echo json_encode([
            'success' => true,
            'teachers' => [],
            'total' => 0,
            'message' => 'No hay periodo académico activo'
        ]);
        exit;
    }

    // Query principal para obtener profesores
    $query = "
        SELECT 
            d.id,
            d.cedula,
            d.nombres,
            d.apellidos,
            d.especialidad,
            d.telefono,
            d.direccion,
            u.url_foto
        FROM docentes d
        LEFT JOIN usuarios u ON d.usuario_id = u.id
        WHERE d.estado_id = 1
        ORDER BY d.apellidos, d.nombres
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Para cada profesor, obtener sus materias
    foreach ($teachers as &$teacher) {
        $teacher['nombre_completo'] = trim($teacher['nombres'] . ' ' . $teacher['apellidos']);

        // Obtener materias del profesor
        $materiasQuery = "
            SELECT DISTINCT m.nombre
            FROM cargas_academicas ca
            INNER JOIN materias m ON ca.materia_id = m.id
            WHERE ca.docente_id = ?
            AND ca.periodo_id = ?
            AND ca.estado_id = 1
            AND m.estado_id = 1
            ORDER BY m.nombre
        ";

        $materiasStmt = $pdo->prepare($materiasQuery);
        $materiasStmt->execute([$teacher['id'], $periodo['id']]);
        $materias = $materiasStmt->fetchAll(PDO::FETCH_COLUMN);

        $teacher['materias'] = $materias;
        $teacher['materias_count'] = count($materias);

        // Determinar estado de carga (color badge)
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
    error_log("Error en get_teachers.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al cargar profesores: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en get_teachers.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
