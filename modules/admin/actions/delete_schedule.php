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
    $scheduleId = isset($data['id']) ? intval($data['id']) : null;

    if (!$scheduleId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de horario requerido']);
        exit;
    }

    // Soft delete: cambiar estado a "Eliminado" (asumiendo estado_id = 3)
    $stmt = $pdo->prepare("
        UPDATE horarios 
        SET estado_id = 3, 
            actualizado_el = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([$scheduleId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Horario eliminado exitosamente'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Horario no encontrado']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar horario: ' . $e->getMessage()]);
}
