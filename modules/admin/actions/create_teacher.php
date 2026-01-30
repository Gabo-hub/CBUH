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
    $cedula = $data['cedula'] ?? '';
    $nombres = $data['nombres'] ?? '';
    $apellidos = $data['apellidos'] ?? '';
    $especialidad = $data['especialidad'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $fechaNacimiento = $data['fecha_nacimiento'] ?? null;
    $lugarNacimiento = $data['lugar_nacimiento'] ?? '';
    $direccion = $data['direccion'] ?? '';
    $resumenProfesional = $data['resumen_profesional'] ?? '';

    if (empty($cedula) || empty($nombres) || empty($apellidos)) {
        http_response_code(400);
        echo json_encode(['error' => 'Cédula, nombres y apellidos son campos requeridos']);
        exit;
    }

    // Verificar si la cédula ya existe
    $checkStmt = $pdo->prepare("SELECT id FROM docentes WHERE cedula = ? AND estado_id = 1");
    $checkStmt->execute([$cedula]);
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Ya existe un profesor con esta cédula']);
        exit;
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar profesor
    $insertStmt = $pdo->prepare("
        INSERT INTO docentes (
            cedula, 
            nombres, 
            apellidos, 
            especialidad, 
            telefono, 
            fecha_nacimiento, 
            lugar_nacimiento, 
            direccion, 
            resumen_profesional,
            estado_id,
            creado_el
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, CURRENT_TIMESTAMP)
    ");

    $insertStmt->execute([
        $cedula,
        $nombres,
        $apellidos,
        $especialidad,
        $telefono,
        $fechaNacimiento,
        $lugarNacimiento,
        $direccion,
        $resumenProfesional
    ]);

    $newId = $pdo->lastInsertId();

    // Si se desea crear usuario (opcional - puede ser NULL)
    $crearUsuario = $data['crear_usuario'] ?? false;
    if ($crearUsuario) {
        $usuarioNombre = $data['usuario'] ?? strtolower($nombres);
        $correo = $data['correo'] ?? '';

        if (!empty($correo)) {
            // Verificar si el correo ya existe
            $checkEmailStmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $checkEmailStmt->execute([$correo]);
            if (!$checkEmailStmt->fetch()) {
                // Crear usuario con contraseña temporal
                $claveTemp = password_hash('profesor123', PASSWORD_DEFAULT);

                $userStmt = $pdo->prepare("
                    INSERT INTO usuarios (usuario, clave_hash, correo, rol_id, estado_id, creado_el)
                    VALUES (?, ?, ?, 4, 1, CURRENT_TIMESTAMP)
                ");
                $userStmt->execute([$usuarioNombre, $claveTemp, $correo]);
                $usuarioId = $pdo->lastInsertId();

                // Vincular usuario con docente
                $updateStmt = $pdo->prepare("UPDATE docentes SET usuario_id = ? WHERE id = ?");
                $updateStmt->execute([$usuarioId, $newId]);
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Profesor creado exitosamente',
        'id' => $newId
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    error_log("Error en create_teacher.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al crear profesor: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    error_log("Error general en create_teacher.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
