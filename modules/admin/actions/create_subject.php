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

    // Validar campos requeridos
    $codigo = $data['codigo'] ?? '';
    $nombre = $data['nombre'] ?? '';
    $año_materia = $data['año_materia'] ?? null;
    $creditos = $data['creditos'] ?? null;
    $descripcion = $data['descripcion'] ?? '';

    if (empty($codigo) || empty($nombre)) {
        http_response_code(400);
        echo json_encode(['error' => 'Código y nombre son campos requeridos']);
        exit;
    }

    // Verificar si el código ya existe
    $checkStmt = $pdo->prepare("SELECT id FROM materias WHERE codigo = ? AND estado_id = 1");
    $checkStmt->execute([$codigo]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Ya existe una materia con este código']);
        exit;
    }

    // Insertar materia
    $insertStmt = $pdo->prepare("
        INSERT INTO materias (
            codigo, 
            nombre, 
            año_materia, 
            creditos, 
            descripcion,
            estado_id
        ) VALUES (?, ?, ?, ?, ?, 1)
    ");

    $insertStmt->execute([
        $codigo,
        $nombre,
        $año_materia,
        $creditos,
        $descripcion
    ]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Materia creada exitosamente',
        'id' => $newId
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en create_subject.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al crear materia: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en create_subject.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
