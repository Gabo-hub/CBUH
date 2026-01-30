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

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    $pdo = getDBConnection();

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de materia requerido']);
        exit;
    }

    // Soft delete - cambiar estado_id a 3 (eliminado)
    $stmt = $pdo->prepare("UPDATE materias SET estado_id = 3 WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Materia eliminada exitosamente'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Materia no encontrada']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en delete_subject.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al eliminar materia: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en delete_subject.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
