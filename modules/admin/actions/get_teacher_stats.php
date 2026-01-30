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

    // Obtener el periodo académico activo
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
            'total_hours' => 0,
            'available_slots' => 0,
            'teachers_at_max' => 0,
            'active_subjects' => 0
        ]);
        exit;
    }

    $periodoId = $periodo['id'];

    // Calcular total de horas semanales
    $hoursQuery = "
        SELECT 
            COALESCE(SUM(
                EXTRACT(EPOCH FROM (h.hora_fin - h.hora_inicio)) / 3600
            ), 0) as total_hours
        FROM horarios h
        INNER JOIN cargas_academicas ca ON h.carga_academica_id = ca.id
        WHERE ca.periodo_id = ?
        AND h.estado_id = 1
        AND ca.estado_id = 1
    ";
    $hoursStmt = $pdo->prepare($hoursQuery);
    $hoursStmt->execute([$periodoId]);
    $hoursResult = $hoursStmt->fetch();
    $totalHours = round($hoursResult['total_hours'] ?? 0, 1);

    // Contar profesores con slots disponibles
    $availableSlots = 0;
    $teachersAtMax = 0;

    $teachersQuery = "
        SELECT d.id
        FROM docentes d
        WHERE d.estado_id = 1
    ";
    $teachersStmt = $pdo->query($teachersQuery);

    while ($teacher = $teachersStmt->fetch()) {
        $countQuery = "
            SELECT COUNT(DISTINCT ca.materia_id) as count
            FROM cargas_academicas ca
            WHERE ca.docente_id = ?
            AND ca.periodo_id = ?
            AND ca.estado_id = 1
        ";
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute([$teacher['id'], $periodoId]);
        $count = $countStmt->fetch()['count'];

        if ($count < 5) {
            $availableSlots++;
        }
        if ($count >= 5) {
            $teachersAtMax++;
        }
    }

    // Contar materias activas en el periodo
    $subjectsQuery = "
        SELECT COUNT(DISTINCT m.id) as active_subjects
        FROM materias m
        INNER JOIN cargas_academicas ca ON m.id = ca.materia_id
        WHERE ca.periodo_id = ?
        AND ca.estado_id = 1
        AND m.estado_id = 1
    ";
    $subjectsStmt = $pdo->prepare($subjectsQuery);
    $subjectsStmt->execute([$periodoId]);
    $subjectsResult = $subjectsStmt->fetch();
    $activeSubjects = intval($subjectsResult['active_subjects'] ?? 0);

    echo json_encode([
        'success' => true,
        'total_hours' => $totalHours,
        'available_slots' => $availableSlots,
        'teachers_at_max' => $teachersAtMax,
        'active_subjects' => $activeSubjects
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en get_teacher_stats.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al calcular estadísticas: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en get_teacher_stats.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
