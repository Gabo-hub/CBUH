<?php
session_start();
require_once '../../../config/database.php';
require_once '../../../config/api_helper.php';
require_once '../../../includes/supabase_storage.php';

// Verificación de autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    ApiResponse::error('No autorizado', 403);
}

// Debug log setup
$logFile = __DIR__ . '/debug_register.log';
function logDebug($msg)
{
    global $logFile;
    @file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Capturar todo el buffer para evitar salidas basura
ob_start();

try {
    logDebug("--- Iniciando proceso de registro ---");

    // 1. Recoger datos
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $cedula = $_POST['cedula'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $lugar_nacimiento = $_POST['lugar_nacimiento'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    if (empty($nombres) || empty($apellidos) || empty($cedula)) {
        logDebug("Error: Campos requeridos vacíos");
        ApiResponse::error('Nombres, apellidos y cédula son obligatorios.');
    }

    $pdo = getDBConnection();

    // 2. Duplicados
    $stmt = $pdo->prepare("SELECT id FROM estudiantes WHERE cedula = ?");
    $stmt->execute([$cedula]);
    if ($stmt->fetch()) {
        logDebug("Error: Cédula duplicada ($cedula)");
        ApiResponse::error('Esta cédula ya existe.');
    }

    $pdo->beginTransaction();

    // 3. Crear Usuario
    $userLogin = str_replace(['.', '-', ' '], '', $cedula);
    $passHash = password_hash('Cbuh123*', PASSWORD_DEFAULT);

    logDebug("Creando usuario con login: $userLogin");
    $stmtUser = $pdo->prepare("INSERT INTO usuarios (usuario, clave_hash, correo, rol_id, estado_id) VALUES (?, ?, ?, (SELECT id FROM roles WHERE nombre = 'estudiante' LIMIT 1), 1) RETURNING id");
    $stmtUser->execute([$userLogin, $passHash, $correo ?: null]);
    $usuario_id = $stmtUser->fetch()['id'];
    logDebug("Usuario id: $usuario_id");

    // 4. Crear Estudiante
    logDebug("Insertando estudiante...");
    $stmtEst = $pdo->prepare("INSERT INTO estudiantes (usuario_id, nombres, apellidos, cedula, telefono, fecha_nacimiento, lugar_nacimiento, direccion, estado_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1) RETURNING id");
    $stmtEst->execute([$usuario_id, $nombres, $apellidos, $cedula, $telefono, $fecha_nacimiento ?: null, $lugar_nacimiento, $direccion]);
    $estudiante_id = $stmtEst->fetch()['id'];
    logDebug("Estudiante id: $estudiante_id");

    // 5. Archivos (Supabase)
    $storage = new SupabaseStorage();

    // Foto de Perfil
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        logDebug("Subiendo foto de perfil...");
        $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        $res = $storage->upload("documents/{$estudiante_id}/perfil.{$ext}", $_FILES['foto_perfil']['tmp_name'], $_FILES['foto_perfil']['type']);
        if ($res) {
            logDebug("Exito foto profile: $res");
            $pdo->prepare("INSERT INTO documentos_estudiantes (estudiante_id, tipo_documento, url_archivo, nombre_original, tamano_bytes, estado_id) VALUES (?, 'foto_perfil', ?, ?, ?, 1)")
                ->execute([$estudiante_id, $res, $_FILES['foto_perfil']['name'], $_FILES['foto_perfil']['size']]);

            // Sincronizar con la tabla usuarios para visualización rápida
            $pdo->prepare("UPDATE usuarios SET url_foto = ? WHERE id = ?")
                ->execute([$res, $usuario_id]);
        } else {
            logDebug("Falla subida foto profile.");
        }
    }

    // Resto de documentos
    $docs = ['doc_cedula' => 'cedula', 'doc_titulo_bachiller' => 'titulo_bachiller', 'doc_notas_certificadas' => 'notas_certificadas', 'doc_partida_nacimiento' => 'partida_nacimiento'];
    foreach ($docs as $key => $type) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            logDebug("Subiendo documento: $type");
            $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
            $res = $storage->upload("documents/{$estudiante_id}/{$type}.{$ext}", $_FILES[$key]['tmp_name'], $_FILES[$key]['type']);
            if ($res) {
                logDebug("Exito documento $type: $res");
                $pdo->prepare("INSERT INTO documentos_estudiantes (estudiante_id, tipo_documento, url_archivo, nombre_original, tamano_bytes, estado_id) VALUES (?, ?, ?, ?, ?, 1)")
                    ->execute([$estudiante_id, $type, $res, $_FILES[$key]['name'], $_FILES[$key]['size']]);
            } else {
                logDebug("Falla subida documento: $type");
            }
        }
    }

    $pdo->commit();
    logDebug("--- Registro exitoso ID: $estudiante_id ---");

    // Limpiar cualquier salida accidental
    ob_end_clean();
    ApiResponse::success(['id' => $estudiante_id], 'Inscripción finalizada con éxito.');

} catch (Throwable $t) {
    if (isset($pdo) && $pdo->inTransaction())
        $pdo->rollBack();
    $errMsg = $t->getMessage();
    logDebug("EXCEPCIÓN: $errMsg en " . $t->getFile() . ":" . $t->getLine());
    ob_end_clean();
    ApiResponse::error('Error en el registro: ' . $errMsg);
}
