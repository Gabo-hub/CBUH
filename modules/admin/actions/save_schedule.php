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

    // Obtener datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);

    $scheduleId = isset($data['id']) ? intval($data['id']) : null;
    $cargaId = isset($data['carga_id']) ? intval($data['carga_id']) : null;
    $diaSemana = isset($data['dia_semana']) ? intval($data['dia_semana']) : null;
    $horaInicio = $data['hora_inicio'] ?? null;
    $horaFin = $data['hora_fin'] ?? null;
    $aula = $data['aula'] ?? '';

    // Validación básica
    if ($diaSemana === null || !$horaInicio || !$horaFin) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan campos requeridos']);
        exit;
    }

    if ($diaSemana < 1 || $diaSemana > 5) {
        http_response_code(400);
        echo json_encode(['error' => 'Día de semana inválido (debe ser 1-5)']);
        exit;
    }

    // Verificar conflictos de horario
    $conflictQuery = "
        SELECT COUNT(*) as count 
        FROM horarios h
        WHERE h.dia_semana = ?
        AND h.estado_id = 1
        AND (
            (h.hora_inicio <= ? AND h.hora_fin > ?) OR
            (h.hora_inicio < ? AND h.hora_fin >= ?) OR
            (h.hora_inicio >= ? AND h.hora_fin <= ?)
        )
    ";

    $conflictParams = [$diaSemana, $horaInicio, $horaInicio, $horaFin, $horaFin, $horaInicio, $horaFin];

    if ($scheduleId) {
        $conflictQuery .= " AND h.id != ?";
        $conflictParams[] = $scheduleId;
    }

    if ($aula) {
        $conflictQuery .= " AND h.aula = ?";
        $conflictParams[] = $aula;
    }

    $conflictStmt = $pdo->prepare($conflictQuery);
    $conflictStmt->execute($conflictParams);
    $conflict = $conflictStmt->fetch();

    if ($conflict['count'] > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'Conflicto de horario detectado en el aula especificada']);
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    if ($scheduleId) {
        // Actualizar horario existente
        $stmt = $pdo->prepare("
            UPDATE horarios 
            SET dia_semana = ?, 
                hora_inicio = ?, 
                hora_fin = ?, 
                aula = ?,
                actualizado_el = CURRENT_TIMESTAMP
            WHERE id = ? AND estado_id = 1
        ");
        $stmt->execute([$diaSemana, $horaInicio, $horaFin, $aula, $scheduleId]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Horario actualizado exitosamente',
            'id' => $scheduleId
        ]);
    } else {
        // Crear nuevo horario
        if (!$cargaId) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['error' => 'Se requiere carga_id para crear un nuevo horario']);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO horarios (carga_academica_id, dia_semana, hora_inicio, hora_fin, aula, estado_id)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$cargaId, $diaSemana, $horaInicio, $horaFin, $aula]);

        $newId = $pdo->lastInsertId();
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Horario creado exitosamente',
            'id' => $newId
        ]);
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar horario: ' . $e->getMessage()]);
}
