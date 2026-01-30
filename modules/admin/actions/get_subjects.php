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

    // Query para obtener todas las materias
    $query = "
        SELECT 
            id,
            codigo,
            nombre,
            año_materia,
            creditos,
            descripcion
        FROM materias
        WHERE estado_id = 1
        ORDER BY año_materia, codigo
    ";

    $stmt = $pdo->query($query);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar por año
    $subjectsByYear = [
        1 => [],
        2 => [],
        3 => [],
        'sin_año' => []
    ];

    foreach ($subjects as $subject) {
        $year = $subject['año_materia'];
        if ($year && isset($subjectsByYear[$year])) {
            $subjectsByYear[$year][] = $subject;
        } else {
            $subjectsByYear['sin_año'][] = $subject;
        }
    }

    echo json_encode([
        'success' => true,
        'subjects' => $subjects,
        'subjects_by_year' => $subjectsByYear,
        'total' => count($subjects)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en get_subjects.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al cargar materias: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en get_subjects.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
