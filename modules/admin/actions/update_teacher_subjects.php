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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $pdo = getDBConnection();

    $data = json_decode(file_get_contents('php://input'), true);
    $docenteId = isset($data['docente_id']) ? intval($data['docente_id']) : null;
    $materiasIds = $data['materias_ids'] ?? [];

    if (!$docenteId || !is_array($materiasIds)) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    // Validar que no se excedan las 5 materias
    if (count($materiasIds) > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Un profesor no puede tener más de 5 materias asignadas']);
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

    if (!$periodo) {
        http_response_code(400);
        echo json_encode(['error' => 'No hay periodo académico activo']);
        exit;
    }

    $periodoId = $periodo['id'];

    // Iniciar transacción
    $pdo->beginTransaction();

    // Eliminar asignaciones antiguas (soft delete)
    $deleteStmt = $pdo->prepare("
        UPDATE cargas_academicas 
        SET estado_id = 3
        WHERE docente_id = ? 
        AND periodo_id = ?
    ");
    $deleteStmt->execute([$docenteId, $periodoId]);

    // Crear nuevas asignaciones
    $insertStmt = $pdo->prepare("
        INSERT INTO cargas_academicas 
        (periodo_id, materia_id, docente_id, seccion_id, estado_id)
        VALUES (?, ?, ?, 1, 1)
        ON CONFLICT ON CONSTRAINT unique_carga_academica 
        DO UPDATE SET estado_id = 1
    ");

    foreach ($materiasIds as $materiaId) {
        $materiaId = intval($materiaId);
        if ($materiaId > 0) {
            try {
                $insertStmt->execute([$periodoId, $materiaId, $docenteId]);
            } catch (PDOException $e) {
                // Si hay error por duplicado, intentar solo actualizar el estado
                $updateStmt = $pdo->prepare("
                    UPDATE cargas_academicas 
                    SET estado_id = 1 
                    WHERE periodo_id = ? 
                    AND materia_id = ? 
                    AND docente_id = ?
                ");
                $updateStmt->execute([$periodoId, $materiaId, $docenteId]);
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Asignaciones actualizadas exitosamente'
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar asignaciones: ' . $e->getMessage()]);
}
