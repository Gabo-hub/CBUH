<?php
// modules/admin/actions/update_student.php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/api_helper.php';
require_once '../../../includes/supabase_storage.php';

// Verificación de autenticación básica
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    ApiResponse::error('No autorizado', 403);
}

// Debug log setup (opcional pero útil)
$logFile = __DIR__ . '/debug_register.log';
function logDebug($msg)
{
    global $logFile;
    @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Iniciar buffer para evitar salidas accidentales
ob_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $id = $_POST['student_id'] ?? null;
    if (!$id) {
        throw new Exception("ID de estudiante no proporcionado.");
    }

    // Datos de entrada
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $telefono = $_POST['telefono'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $fecha_nac = $_POST['fecha_nacimiento'] ?? null;
    $lugar_nac = $_POST['lugar_nacimiento'] ?? null;
    $anio = $_POST['anio_actual'] ?? 1;
    $estado = $_POST['estado_id'] ?? 1;

    // Datos de usuario
    $usuario_login = $_POST['usuario_login'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $nueva_clave = $_POST['nueva_clave'] ?? '';

    $pdo = getDBConnection();
    $pdo->beginTransaction();

    // 1. Obtener usuario_id del estudiante
    $stmtId = $pdo->prepare("SELECT usuario_id FROM estudiantes WHERE id = ?");
    $stmtId->execute([$id]);
    $usuario_id = $stmtId->fetchColumn();

    // 2. Procesar nueva foto si existe
    $storage = new SupabaseStorage();
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        logDebug("Actualizando foto de perfil para estudiante ID $id");
        $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        // Usamos el ID de usuario para el nombre del archivo de perfil
        $fileName = $usuario_id ? "profile_{$usuario_id}.{$ext}" : "estudiante_{$id}.{$ext}";
        $newPhotoUrl = $storage->upload("profiles/{$fileName}", $_FILES['foto_perfil']['tmp_name'], $_FILES['foto_perfil']['type']);

        if ($newPhotoUrl && $usuario_id) {
            $pdo->prepare("UPDATE usuarios SET url_foto = ? WHERE id = ?")->execute([$newPhotoUrl, $usuario_id]);
        }
    }

    // 3. Actualizar Tabla Usuarios
    if ($usuario_id) {
        $userUpdates = ["usuario = :login", "correo = :correo"];
        $userParams = ['login' => $usuario_login, 'correo' => $correo ?: null, 'uid' => $usuario_id];

        if (!empty($nueva_clave)) {
            $userUpdates[] = "clave_hash = :pass";
            $userParams['pass'] = password_hash($nueva_clave, PASSWORD_DEFAULT);
        }

        $sqlUser = "UPDATE usuarios SET " . implode(", ", $userUpdates) . " WHERE id = :uid";
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute($userParams);
    }

    // 4. Actualizar Tabla Estudiantes
    $stmt = $pdo->prepare("
        UPDATE estudiantes 
        SET 
            nombres = :nombres,
            apellidos = :apellidos,
            cedula = :cedula,
            telefono = :telefono,
            direccion = :direccion,
            fecha_nacimiento = :fecha_nac,
            lugar_nacimiento = :lugar_nac,
            año_actual = :anio,
            estado_id = :estado,
            actualizado_el = NOW()
        WHERE id = :id
    ");

    $stmt->execute([
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'cedula' => $cedula,
        'telefono' => $telefono ?: null,
        'direccion' => $direccion ?: null,
        'fecha_nac' => $fecha_nac ?: null,
        'lugar_nac' => $lugar_nac ?: null,
        'anio' => $anio,
        'estado' => $estado,
        'id' => $id
    ]);

    $pdo->commit();

    // Limpiar buffer y enviar éxito
    ob_end_clean();
    ApiResponse::success([
        'id' => $id,
        'nombres' => $nombres,
        'apellidos' => $apellidos,
        'cedula' => $cedula
    ], 'Estudiante actualizado correctamente');

} catch (Throwable $t) {
    if (isset($pdo) && $pdo->inTransaction())
        $pdo->rollBack();
    logDebug("ERROR EN UPDATE: " . $t->getMessage());
    ob_end_clean();
    ApiResponse::error('Fallo en la actualización: ' . $t->getMessage());
}